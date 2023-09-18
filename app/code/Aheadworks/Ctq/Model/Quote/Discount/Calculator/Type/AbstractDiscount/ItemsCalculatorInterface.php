<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount;

use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Aheadworks\Ctq\Model\Metadata\Negotiation\NegotiatedDiscountInterface;

/**
 * Interface ItemsCalculatorInterface
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount
 */
interface ItemsCalculatorInterface
{
    /**
     * Calculate item discount
     *
     * @param AbstractItem[] $cartItems
     * @param NegotiatedDiscountInterface $negotiatedDiscount
     * @return QuoteItemDiscount[]
     */
    public function calculate($cartItems, $negotiatedDiscount);
}
