<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart;

use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\QuoteCartInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ToQuoteCart
 * @package Aheadworks\Ctq\Model\Cart
 */
class ToQuoteCart
{
    /**
     * @var QuoteCartInterfaceFactory
     */
    private $quoteCartFactory;

    /**
     * @var Converter
     */
    private $cartConverter;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param QuoteCartInterfaceFactory $quoteCartFactory
     * @param Converter $cartConverter
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        QuoteCartInterfaceFactory $quoteCartFactory,
        Converter $cartConverter,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->quoteCartFactory = $quoteCartFactory;
        $this->cartConverter = $cartConverter;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Convert cart to quote
     *
     * @param CartInterface|Quote $cart
     * @return QuoteCartInterface
     */
    public function convert($cart)
    {
        $cartArray = $this->cartConverter->toArray($cart);
        $quoteCart = $this->quoteCartFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteCart,
            $cartArray,
            QuoteCartInterface::class
        );

        return $quoteCart;
    }
}
