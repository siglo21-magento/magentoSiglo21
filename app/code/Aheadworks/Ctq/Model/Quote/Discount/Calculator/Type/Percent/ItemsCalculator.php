<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\Percent;

use Aheadworks\Ctq\Model\Quote\Discount\Calculator\DiscountCalculatorInterface;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item\Processor;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item\Validator;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item\Distributor;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount\ItemsCalculatorInterface;
use Aheadworks\Ctq\Model\Metadata\Quote\Item\DiscountFactory as QuoteItemDiscountFactory;
use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;

/**
 * Class ItemsCalculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\Percent
 */
class ItemsCalculator implements ItemsCalculatorInterface
{
    /**
     * @var QuoteItemDiscountFactory
     */
    private $quoteItemDiscountFactory;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var Distributor
     */
    private $distributor;

    /**
     * @param QuoteItemDiscountFactory $quoteItemDiscountFactory
     * @param Validator $validator
     * @param Processor $processor
     * @param Distributor $distributor
     */
    public function __construct(
        QuoteItemDiscountFactory $quoteItemDiscountFactory,
        Validator $validator,
        Processor $processor,
        Distributor $distributor
    ) {
        $this->quoteItemDiscountFactory = $quoteItemDiscountFactory;
        $this->validator = $validator;
        $this->processor = $processor;
        $this->distributor = $distributor;
    }

    /**
     * @inheritdoc
     */
    public function calculate($items, $negotiatedDiscount)
    {
        $itemsDiscount = [];
        foreach ($items as $item) {
            if ($item->getParentItem() || !$this->validator->canApplyDiscount($item, $negotiatedDiscount)) {
                continue;
            }

            $calculateType = $item->getAwCtqCalculateType();
            $itemPrice = $this->processor->getTotalItemPrice($item);
            $baseItemPrice = $this->processor->getTotalItemBasePrice($item);
            $percent = $negotiatedDiscount->getDiscountValue() / 100;
            $isNeedCalculate = $calculateType == DiscountCalculatorInterface::CALCULATE_PER_ITEM;

            if ($isNeedCalculate) {
                $percent = $item->getAwCtqPercent() / 100;
            }

            if ($calculateType == DiscountCalculatorInterface::CALCULATE_RESET) {
                $percent = 0;
            }

            /** @var QuoteItemDiscount $quoteItemDiscount */
            $quoteItemDiscount = $this->quoteItemDiscountFactory->create();
            $quoteItemDiscount
                ->setPercent(
                    $isNeedCalculate
                        ? $percent * 100
                        : $negotiatedDiscount->getDiscountValue()
                )
                ->setAmount($itemPrice * $percent)
                ->setBaseAmount($baseItemPrice * $percent)
                ->setItem($item);

            $itemsDiscount[] = $this->distributor->distribute($quoteItemDiscount);
        }

        return $itemsDiscount;
    }
}
