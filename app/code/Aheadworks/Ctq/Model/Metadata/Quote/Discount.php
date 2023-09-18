<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Metadata\Quote;

use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;

/**
 * Class Discount
 *
 * @package Aheadworks\Ctq\Model\Metadata\Quote
 */
class Discount
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $baseAmount;

    /**
     * @var float
     */
    private $percentAmount;

    /**
     * @var string
     */
    private $amountType;

    /**
     * @var QuoteItemDiscount[]
     */
    private $items;

    /**
     * Discount constructor
     */
    public function __construct()
    {
        $this
            ->setAmount(0)
            ->setBaseAmount(0)
            ->setPercentAmount(0)
            ->setItems([]);
    }

    /**
     * Is discount available
     *
     * @return bool
     */
    public function isDiscountAvailable()
    {
        return $this->getAmount() > 0;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get base amount
     *
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount)
    {
        $this->baseAmount = $baseAmount;
        return $this;
    }

    /**
     * Get percent amount
     *
     * @return float
     */
    public function getPercentAmount()
    {
        return $this->percentAmount;
    }

    /**
     * Set percent amount
     *
     * @param float $percentAmount
     * @return $this
     */
    public function setPercentAmount($percentAmount)
    {
        $this->percentAmount = $percentAmount;
        return $this;
    }

    /**
     * Get amount type
     *
     * @return string
     */
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * Set amount type
     *
     * @param string $amountType
     * @return $this
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;
        return $this;
    }

    /**
     * Get items
     *
     * @return QuoteItemDiscount[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items
     *
     * @param QuoteItemDiscount[] $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }
}
