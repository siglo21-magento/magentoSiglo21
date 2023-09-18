<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api;

/**
 * Interface CustomerManagementInterface
 * @api
 */
interface CustomerManagementInterface
{
    /**
     * Check if credit limit is available for customer
     *
     * @param int $customerId
     * @return bool
     */
    public function isCreditLimitAvailable($customerId);

    /**
     * Check if credit limit is configured by admin
     *
     * @param int $customerId
     * @return bool
     */
    public function isCreditLimitCustom($customerId);

    /**
     * Get credit limit amount
     *
     * @param int $customerId
     * @param string|null $currency
     * @return float|null in case it's not configured at all
     */
    public function getCreditLimitAmount($customerId, $currency = null);

    /**
     * Get credit balance amount
     *
     * @param int $customerId
     * @param string|null $currency
     * @return float
     */
    public function getCreditBalanceAmount($customerId, $currency = null);

    /**
     * Get credit available amount
     *
     * @param int $customerId
     * @param string|null $currency
     * @return float
     */
    public function getCreditAvailableAmount($customerId, $currency = null);
}
