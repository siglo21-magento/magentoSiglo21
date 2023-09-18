<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class TransactionManagement
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model
 */
class TransactionManagement
{
    /**
     * @var SummaryManagement
     */
    private $summaryManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var \Aheadworks\StoreCredit\Api\TransactionManagementInterface
     */
    private $transactionService;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param SummaryManagement $summaryManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param ObjectManagerInterface $objectManager
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        SummaryManagement $summaryManagement,
        CompanyUserManagementInterface $companyUserManagement,
        ObjectManagerInterface $objectManager,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->summaryManagement = $summaryManagement;
        $this->companyUserManagement = $companyUserManagement;
        $this->customerRepository = $customerRepository;
        $this->transactionService =
            $objectManager->get(\Aheadworks\StoreCredit\Api\TransactionManagementInterface::class);
    }

    /**
     * Move all customer balance to company balance
     *
     * @param int $customerId
     * @return bool
     * @throws LocalizedException
     */
    public function moveCustomerBalanceToCompanyBalance($customerId)
    {
        $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);
        if ($rootCustomer) {
            $user = $this->customerRepository->getById($customerId);
            $userBalance = (int)$this->summaryManagement->getCurrentCustomerBalance($customerId);
            if (!$userBalance) {
                return false;
            }

            $comment = __('Manual Transaction. Customer balance has been moved to company balance');
            $this->createTransaction($user, -$userBalance, $comment);

            $comment = __('Manual Transaction. Customer balance has been received from user');
            $this->createTransaction($rootCustomer, $userBalance, $comment);

            return true;
        }

        return false;
    }

    /**
     * Create transaction
     *
     * @param CustomerInterface $customer
     * @param int $balance
     * @param string $comment
     * @return bool
     * @throws CouldNotSaveException
     */
    private function createTransaction($customer, $balance, $comment)
    {
        /** @var \Aheadworks\StoreCredit\Api\Data\TransactionInterface $transaction */
        $transaction = $this->transactionService->createTransaction(
            $customer,
            $balance,
            \Aheadworks\StoreCredit\Model\Source\TransactionType::BALANCE_ADJUSTED_BY_ADMIN,
            $comment,
            null,
            $comment,
            $customer->getWebsiteId(),
            \Aheadworks\StoreCredit\Model\Source\NotifiedStatus::NO,
            [],
            null
        );
        $currentBalance = $this->summaryManagement->getCurrentCustomerBalance($customer->getId());
        $transaction->setCurrentBalance($currentBalance);
        $this->transactionService->saveTransaction($transaction);
        return true;
    }
}
