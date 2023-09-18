<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface SellerQuoteManagementInterface
 * @api
 */
interface SellerQuoteManagementInterface
{
    /**
     * Change quote status
     *
     * @param int $quoteId
     * @param string $status
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changeStatus($quoteId, $status);

    /**
     * Create quote
     *
     * @param int $cartId
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createQuote($cartId, $quote);

    /**
     * Update quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateQuote($quote);

    /**
     * Retrieve native cart object by quote
     *
     * Native cart object will be created again in case it was removed
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface|int $quote
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCartByQuote($quote);

    /**
     * Make a quote duplication
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function copyQuote($quote);

    /**
     * Sell quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return void
     */
    public function sell($quote);
}
