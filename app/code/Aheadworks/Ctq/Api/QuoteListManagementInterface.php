<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface QuoteListManagementInterface
 * @package Aheadworks\Ctq\Api
 */
interface QuoteListManagementInterface
{
    /**
     * Creates an empty quote for a guest
     *
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createQuoteList();

    /**
     * Creates an empty quote for a specified customer
     *
     * @param int $customerId
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createQuoteListForCustomer($customerId);

    /**
     * Returns information for the quote list for a specified id
     *
     * @param int $quoteId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteList($quoteId);

    /**
     * Returns information for the quote list for a specified customer
     *
     * @param int $customerId
     * @return \Magento\Quote\Api\Data\CartInterface|null
     */
    public function getQuoteListForCustomer($customerId);

    /**
     * Merge quote lists
     *
     * @param \Magento\Quote\Api\Data\CartInterface $customerQuote
     * @param \Magento\Quote\Api\Data\CartInterface $quoteToMerge
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function mergeQuoteLists($customerQuote, $quoteToMerge);
}
