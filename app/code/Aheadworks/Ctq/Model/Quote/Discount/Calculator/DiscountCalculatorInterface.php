<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator;

use Aheadworks\Ctq\Model\Metadata\Quote\Discount as QuoteDiscount;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Api\Data\AddressInterface;
use Aheadworks\Ctq\Model\Metadata\Negotiation\NegotiatedDiscountInterface;

/**
 * Interface DiscountCalculatorInterface
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator
 */
interface DiscountCalculatorInterface
{
    /**
     * Discount calculate types
     */
    const CALCULATE_RESET = 2;
    const CALCULATE_PER_ITEM = 1;
    const CALCULATE_DEFAULT = 0;

    /**
     * Calculate discount
     *
     * @param AbstractItem[] $cartItems
     * @param AddressInterface $address
     * @param NegotiatedDiscountInterface $negotiatedDiscount
     * @return QuoteDiscount
     */
    public function calculate($cartItems, $address, $negotiatedDiscount);
}
