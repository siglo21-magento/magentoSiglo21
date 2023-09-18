<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Metadata\Quote\Item;

use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class Discount
 *
 * @package Aheadworks\Ctq\Model\Metadata\Quote\Item
 */
class Discount
{
    /**
     * @var AbstractItem
     */
    private $item;

    /**
     * @var float
     */
    private $percent;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $baseAmount;

    /**
     * @var Discount[]
     */
    private $children;

    /**
     * Item constructor
     */
    public function __construct()
    {
        $this
            ->setPercent(0)
            ->setAmount(0)
            ->setBaseAmount(0)
            ->setChildren([]);
    }

    /**
     * Set item
     *
     * @return AbstractItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get item
     *
     * @param AbstractItem $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set percent
     *
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
        return $this;
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
     * Get children
     *
     * @return Discount[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children
     *
     * @param Discount[] $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }
}
