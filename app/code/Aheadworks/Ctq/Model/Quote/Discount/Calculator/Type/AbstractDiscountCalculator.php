<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type;

use Aheadworks\Ctq\Model\Quote\Discount\Calculator\DiscountCalculatorInterface;
use Aheadworks\Ctq\Model\Metadata\Quote\DiscountFactory as QuoteDiscountFactory;
use Aheadworks\Ctq\Model\Metadata\Quote\Discount;
use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount\ItemsCalculatorInterface;

/**
 * Class AbstractDiscountCalculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type
 */
class AbstractDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * @var QuoteDiscountFactory
     */
    protected $quoteDiscountFactory;

    /**
     * @var ItemsCalculatorInterface
     */
    protected $itemsCalculator;

    /**
     * @param QuoteDiscountFactory $quoteDiscountFactory
     * @param ItemsCalculatorInterface $itemsCalculator
     */
    public function __construct(
        QuoteDiscountFactory $quoteDiscountFactory,
        ItemsCalculatorInterface $itemsCalculator
    ) {
        $this->quoteDiscountFactory = $quoteDiscountFactory;
        $this->itemsCalculator = $itemsCalculator;
    }

    /**
     * @inheritdoc
     */
    public function calculate($items, $address, $negotiatedDiscount)
    {
        /** @var Discount $quoteDiscount */
        $quoteDiscount = $this->quoteDiscountFactory->create();
        $quoteItemDiscountList = $this->itemsCalculator->calculate($items, $negotiatedDiscount);

        list($amount, $baseAmount) = $this->calculateItemAmount($quoteItemDiscountList);

        $quoteDiscount
            ->setAmount($amount)
            ->setBaseAmount($baseAmount)
            ->setAmountType($negotiatedDiscount->getDiscountType())
            ->setItems($quoteItemDiscountList);

        return $quoteDiscount;
    }

    /**
     * Calculate item amount
     *
     * @param QuoteItemDiscount[] $quoteItemDiscountList
     * @return array
     */
    protected function calculateItemAmount($quoteItemDiscountList)
    {
        $amount = $baseAmount = 0;
        foreach ($quoteItemDiscountList as $quoteItemDiscount) {
            $item = $quoteItemDiscount->getItem();

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($quoteItemDiscount->getChildren() as $childItemDiscount) {
                    $amount += $childItemDiscount->getAmount();
                    $baseAmount += $childItemDiscount->getBaseAmount();
                }
            } else {
                $amount += $quoteItemDiscount->getAmount();
                $baseAmount += $quoteItemDiscount->getBaseAmount();
            }
        }

        return [$amount, $baseAmount];
    }
}
