<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\Backend;

use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface as ParamsInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class BalanceUpdater
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Backend
 */
class BalanceUpdater
{
    /**
     * @var CreditLimitManagementInterface
     */
    private $creditLimitManagement;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        CreditLimitManagementInterface $creditLimitManagement,
        CustomerManagementInterface $customerManagement
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
        $this->customerManagement = $customerManagement;
    }

    /**
     * Update credit limit
     *
     * @param int $customerId
     * @param array $creditLimitData
     * @param array $defaultData
     * @return bool
     * @throws LocalizedException
     */
    public function updateCreditLimit($customerId, $creditLimitData, $defaultData)
    {
        $useDefaultCreditLimit = $defaultData[ParamsInterface::CREDIT_LIMIT] ?? false;

        $creditLimit = $creditLimitData[ParamsInterface::CREDIT_LIMIT] ?? null;
        if (empty($creditLimit) && !strlen($creditLimit)) {
            return false;
        }

        $adminComment = $creditLimitData['credit_limit_' . ParamsInterface::COMMENT_TO_ADMIN] ?? null;
        if (!(bool)$useDefaultCreditLimit && $this->isCreditLimitChanged($customerId, $creditLimit)) {
            return $this->creditLimitManagement->updateCreditLimit(
                $customerId,
                $creditLimit,
                $adminComment
            );
        }
        if ((bool)$useDefaultCreditLimit && $this->customerManagement->isCreditLimitCustom($customerId)) {
            return $this->creditLimitManagement->updateDefaultCreditLimit(
                $customerId,
                0,
                $adminComment
            );
        }

        return false;
    }

    /**
     * Update credit balance
     *
     * @param int $customerId
     * @param array $creditLimitData
     * @return bool
     * @throws LocalizedException
     */
    public function updateCreditBalance($customerId, $creditLimitData)
    {
        $amount = $creditLimitData[ParamsInterface::AMOUNT] ?? null;
        if (empty($amount) && !strlen($amount)) {
            return false;
        }
        if (!$this->customerManagement->isCreditLimitAvailable($customerId)) {
            throw new LocalizedException(__('Please specify Credit Limit for customer before updating amount'));
        }

        $adminComment = $creditLimitData['balance_' . ParamsInterface::COMMENT_TO_ADMIN] ?? null;
        $customerComment = $creditLimitData['balance_' . ParamsInterface::COMMENT_TO_CUSTOMER] ?? null;
        $poNumber = $creditLimitData[ParamsInterface::PO_NUMBER] ?? null;
        $currency = $creditLimitData[ParamsInterface::AMOUNT . '_currency'] ?? null;
        return $this->creditLimitManagement->updateCreditBalance(
            $customerId,
            $amount,
            $currency,
            $adminComment,
            $customerComment,
            $poNumber
        );
    }

    /**
     * Is credit limit changed
     *
     * @param int $customerId
     * @param float $newCreditLimitAmount
     * @return bool
     */
    private function isCreditLimitChanged($customerId, $newCreditLimitAmount)
    {
        $originalCreditLimit = $this->customerManagement->getCreditLimitAmount($customerId);

        return $newCreditLimitAmount != $originalCreditLimit;
    }
}
