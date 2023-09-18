<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Aheadworks\Ctq\Model\Metadata\Negotiation\NegotiatedDiscountInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item
 */
class Validator
{
    /**
     * Can apply discount on item
     *
     * @param AbstractItem $cartItem
     * @param NegotiatedDiscountInterface $negotiatedDiscount
     * @return bool
     */
    public function canApplyDiscount($cartItem, $negotiatedDiscount)
    {
        return is_numeric($negotiatedDiscount->getDiscountValue());
    }
}
