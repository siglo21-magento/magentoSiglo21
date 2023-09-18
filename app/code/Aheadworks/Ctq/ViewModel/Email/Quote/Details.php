<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Email\Quote;

use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class Details
 *
 * @package Aheadworks\Ctq\ViewModel\Email\Quote
 */
class Details implements ArgumentInterface
{
    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @param QuoteManagement $quoteManagement
     */
    public function __construct(
        QuoteManagement $quoteManagement
    ) {
        $this->quoteManagement = $quoteManagement;
    }

    /**
     * Retrieve cart instance
     *
     * @param QuoteInterface $quote
     * @return CartInterface|Quote|null
     */
    public function getCart($quote)
    {
        try {
            /** @var CartInterface|Quote $cart */
            $cart = $this->quoteManagement->getCartByQuoteId($quote->getId());
        } catch (LocalizedException $exception) {
            $cart = null;
        }

        return $cart;
    }

    /**
     * Retrieve array of cart items to render
     *
     * @param CartInterface|Quote $cart
     * @return QuoteItem[]|CartItemInterface[]
     */
    public function getItemsToRender($cart)
    {
        return $cart->getAllVisibleItems();
    }

    /**
     * Retrieve cart item type
     *
     * @param QuoteItem|CartItemInterface $cartItem
     * @return string
     */
    public function getItemType($cartItem)
    {
        return $cartItem->getProductType();
    }
}
