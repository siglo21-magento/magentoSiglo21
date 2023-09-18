<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\CreditLimit\Model\User\UserRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdatedBy
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class UpdatedBy implements TransactionBuilderInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param UserContextInterface $userContext
     * @param UserRepository $userRepository
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        UserContextInterface $userContext,
        UserRepository $userRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->userContext = $userContext;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        return $this->userContext->getUserId() && $this->userContext->getUserType();
    }

    /**
     * @inheritdoc
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $userId = $this->userContext->getUserId();
        $userType = $this->userContext->getUserType();
        $transaction->setUpdatedBy($this->getUserName($userId, $userType));
    }

    /**
     * Get user name
     *
     * @param int $userId
     * @param int $userType
     * @return string
     * @throws LocalizedException
     */
    private function getUserName($userId, $userType)
    {
        $userName = '';
        try {
            if ($userType == UserContextInterface::USER_TYPE_ADMIN) {
                $user = $this->userRepository->getById($userId);
                $userName = $user->getFirstName() . ' ' . $user->getLastName();
            } elseif ($userType == UserContextInterface::USER_TYPE_CUSTOMER) {
                $customer = $this->customerRepository->getById($userId);
                $userName = $customer->getFirstname() . ' ' . $customer->getLastname();
            }
        } catch (NoSuchEntityException $exception) {
        }

        return $userName;
    }
}
