<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\ProposedPrice\Items;

use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount\Items\AbstractItemCalculator;

/**
 * Class ItemCalculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\ProposedPrice\Items
 */
class ItemCalculator extends AbstractItemCalculator
{
    /**
     * Calculate available amount
     *
     * @param float $amount
     * @return $this
     */
    protected function calculateAvailableAmount($amount)
    {
        $baseAvailableAmount = min(
            $this->metadata->getBaseItemsTotal(),
            $this->metadata->getBaseItemsTotal() - $amount
        );

        $this->metadata
            ->setBaseAvailableAmount($baseAvailableAmount)
            ->setAvailableAmount($this->priceCurrency->convertAndRound($baseAvailableAmount));

        return $this;
    }
}
