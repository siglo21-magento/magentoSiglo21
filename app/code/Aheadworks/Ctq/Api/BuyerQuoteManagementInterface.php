<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface BuyerQuoteManagementInterface
 * @api
 */
interface BuyerQuoteManagementInterface
{
    /**
     * Request a quote
     *
     * @param int $cartId
     * @param string $quoteName
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface|null $comment
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function requestQuote($cartId, $quoteName, $comment = null);

    /**
     * Request a quote list
     *
     * @param int $cartId
     * @param string $quoteName
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface|null $comment
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function requestQuoteList($cartId, $quoteName, $comment = null);

    /**
     * Update quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Aheadworks\Ctq\Model\Exception\UpdateForbiddenException
     */
    public function updateQuote($quote);

    /**
     * Change quote items order
     * sortOrderMap should be item ids array according to their positions, e.g [1, 2, 3]
     *
     * @param int $quoteId
     * @param int[] $sortOrderMap
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changeQuoteItemsOrder($quoteId, $sortOrderMap = []);

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
     * Restore buyer cart by quoteId
     *
     * @param int $quoteId
     * @param int $storeId
     * @return bool
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function buy($quoteId, $storeId);

    /**
     * Retrieve native cart object by quote
     *
     * Native cart object will be created again in case it was removed
     *
     * @param int|\Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @param int $storeId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCartByQuote($quote, $storeId);

    /**
     * Clear buyer cart
     *
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearCart($cart);

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
}
