<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\SummaryManagement;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SenderPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin
 */
class SenderPlugin
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
     * @param SummaryManagement $summaryManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        SummaryManagement $summaryManagement,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->summaryManagement = $summaryManagement;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Adjust balance variables for company user customer
     *
     * @param \Aheadworks\StoreCredit\Model\Sender $subject
     * @param int $balanceUpdateAction
     * @param CustomerInterface $customer
     * @param string $comment
     * @param string $balance
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeSendUpdateBalanceNotification(
        $subject,
        $balanceUpdateAction,
        $customer,
        $comment,
        $balance,
        $storeId
    ) {
        $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customer->getId());
        if ($rootCustomer) {
            $balance = $this->summaryManagement->getCurrentCustomerBalanceBaseCurrency($customer);
        }

        return [
            $balanceUpdateAction,
            $customer,
            $comment,
            $balance,
            $storeId
        ];
    }
}
