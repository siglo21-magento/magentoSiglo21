<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface BuyerPermissionManagementInterface
 * @api
 */
interface BuyerPermissionManagementInterface
{
    /**
     * Check if can buy quote or not
     *
     * @param int $quoteId
     * @return bool
     */
    public function canBuyQuote($quoteId);

    /**
     * Check if can request quote or not
     *
     * @param int $cartId
     * @return bool
     */
    public function canRequestQuote($cartId);

    /**
     * Check if can request quote list or not
     *
     * @param int $cartId
     * @return bool
     */
    public function canRequestQuoteList($cartId);

    /**
     * Check if allow quotes or not
     *
     * @param int $customerId
     * @param int $storeId
     * @return bool
     */
    public function isAllowQuotesForCustomer($customerId, $storeId);

    /**
     * Check if allow quote list
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return bool
     */
    public function isAllowQuoteList($customerGroupId, $storeId);

    /**
     * Check if allow quote update
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isAllowQuoteUpdate($websiteId = null);

    /**
     * Check if allow quote items sorting
     *
     * @param int $quoteId
     * @return bool
     * @throws \Exception
     */
    public function isAllowItemsSorting($quoteId);
}
