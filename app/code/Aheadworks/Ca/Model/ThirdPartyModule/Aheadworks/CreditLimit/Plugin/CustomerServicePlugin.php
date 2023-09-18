<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;

/**
 * Class CustomerServicePlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin
 */
class CustomerServicePlugin
{
    /**
     * @var CreditLimitManagement
     */
    private $creditLimitManagement;

    /**
     * @param CreditLimitManagement $creditLimitManagement
     */
    public function __construct(
        CreditLimitManagement $creditLimitManagement
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
    }

    /**
     * Check if credit limit is allowed
     *
     * @param \Aheadworks\CreditLimit\Api\CustomerManagementInterface $subject
     * @param callable $proceed
     * @param int $customerId
     * @return bool
     */
    public function aroundIsCreditLimitAvailable($subject, callable $proceed, $customerId)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        $isAllowed = $proceed($customerId);

        return $isAllowed && $this->creditLimitManagement->isAvailableViewAndUse();
    }

    /**
     * Change customer to company admin if required
     *
     * @param \Aheadworks\CreditLimit\Api\CustomerManagementInterface $subject
     * @param int $customerId
     * @return array
     */
    public function beforeIsCreditLimitCustom($subject, $customerId)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId];
    }

    /**
     * Change customer to company admin if required
     *
     * @param \Aheadworks\CreditLimit\Api\CustomerManagementInterface $subject
     * @param int $customerId
     * @param string|null $currency
     * @return array
     */
    public function beforeGetCreditLimitAmount($subject, $customerId, $currency = null)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $currency];
    }

    /**
     * Change customer to company admin if required
     *
     * @param \Aheadworks\CreditLimit\Api\CustomerManagementInterface $subject
     * @param int $customerId
     * @param string|null $currency
     * @return array
     */
    public function beforeGetCreditBalanceAmount($subject, $customerId, $currency = null)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $currency];
    }

    /**
     * Change customer to company admin if required
     *
     * @param \Aheadworks\CreditLimit\Api\CustomerManagementInterface $subject
     * @param int $customerId
     * @param string|null $currency
     * @return array
     */
    public function beforeGetCreditAvailableAmount($subject, $customerId, $currency = null)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return [$customerId, $currency];
    }
}
