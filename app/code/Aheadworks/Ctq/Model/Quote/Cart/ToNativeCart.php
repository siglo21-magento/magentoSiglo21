<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Cart;

use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Model\Cart\Converter as CartConverter;

/**
 * Class ToNativeCart
 * @package Aheadworks\Ctq\Model\Quote\Cart
 */
class ToNativeCart
{
    /**
     * @var CartConverter
     */
    private $cartConverter;

    /**
     * @var Converter
     */
    private $quoteCartConverter;

    /**
     * @param CartConverter $cartConverter
     * @param Converter $quoteCartConverter
     */
    public function __construct(
        CartConverter $cartConverter,
        Converter $quoteCartConverter
    ) {
        $this->cartConverter = $cartConverter;
        $this->quoteCartConverter = $quoteCartConverter;
    }

    /**
     * Convert cart to quote
     *
     * @param QuoteCartInterface $cartQuote
     * @param bool $isNew
     * @return CartInterface|Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convert($cartQuote, $isNew = false)
    {
        $cartQuoteArray = $this->quoteCartConverter->toArray($cartQuote);
        $cart = $this->cartConverter->toObject($cartQuoteArray, $isNew);

        return $cart;
    }
}
