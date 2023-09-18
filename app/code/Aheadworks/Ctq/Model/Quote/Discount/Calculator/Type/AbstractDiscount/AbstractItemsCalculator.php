<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount;

use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item\Processor;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item\Validator;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item\Distributor;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount\Items\AbstractItemCalculator;
use Aheadworks\Ctq\Model\Metadata\Negotiation\NegotiatedDiscountInterface;

/**
 * Class AbstractItemsCalculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount
 */
class AbstractItemsCalculator implements ItemsCalculatorInterface
{
    /**
     * @var AbstractItemCalculator
     */
    private $itemCalculator;

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
     * @param AbstractItemCalculator $itemCalculator
     * @param Validator $validator
     * @param Processor $processor
     * @param Distributor $distributor
     */
    public function __construct(
        AbstractItemCalculator $itemCalculator,
        Validator $validator,
        Processor $processor,
        Distributor $distributor
    ) {
        $this->itemCalculator = $itemCalculator;
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
        $validItems = $this->getValidItems($items, $negotiatedDiscount);
        $this->itemCalculator->init($validItems, $negotiatedDiscount->getDiscountValue());
        foreach ($validItems as $item) {
            $quoteItemDiscount = $this->itemCalculator->calculateItemAmount($item);
            $itemsDiscount[] = $this->distributor->distribute($quoteItemDiscount);
        }

        return $itemsDiscount;
    }

    /**
     * Get valid items
     *
     * @param AbstractItem[] $items
     * @param NegotiatedDiscountInterface $negotiatedDiscount
     * @return AbstractItem[]
     */
    private function getValidItems($items, $negotiatedDiscount)
    {
        $validItems = [];
        foreach ($items as $item) {
            if ($item->getParentItem()
                || !$this->validator->canApplyDiscount($item, $negotiatedDiscount)
                || !($this->processor->getTotalItemPrice($item) > 0)
            ) {
                continue;
            }
            $validItems[] = $item;
        }

        return $validItems;
    }
}
