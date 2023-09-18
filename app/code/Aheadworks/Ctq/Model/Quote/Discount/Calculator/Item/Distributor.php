<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item;

use Aheadworks\Ctq\Model\Metadata\Quote\Item\DiscountFactory as QuoteItemDiscountFactory;
use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class Distributor
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item
 */
class Distributor
{
    /**
     * @var QuoteItemDiscountFactory
     */
    private $quoteItemDiscountFactory;

    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverter;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var array
     */
    private $roundingDelta = [];

    /**
     * @var array
     */
    private $distributeFields = [
        'amount',
        'base_amount'
    ];

    /**
     * @param QuoteItemDiscountFactory $quoteItemDiscountFactory
     * @param SimpleDataObjectConverter $simpleDataObjectConverter
     * @param PriceCurrencyInterface $priceCurrency
     * @param Processor $processor
     */
    public function __construct(
        QuoteItemDiscountFactory $quoteItemDiscountFactory,
        SimpleDataObjectConverter $simpleDataObjectConverter,
        PriceCurrencyInterface $priceCurrency,
        Processor $processor
    ) {
        $this->quoteItemDiscountFactory = $quoteItemDiscountFactory;
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->priceCurrency = $priceCurrency;
        $this->processor = $processor;
    }

    /**
     * Distribute item if needed
     *
     * @param QuoteItemDiscount $quoteItemDiscount
     * @return QuoteItemDiscount
     */
    public function distribute($quoteItemDiscount)
    {
        $item = $quoteItemDiscount->getItem();
        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            return $this->reset()->distributeProcess($quoteItemDiscount);
        }

        return $quoteItemDiscount;
    }

    /**
     * Reset rounding data before start new process
     *
     * @return $this
     */
    private function reset()
    {
        foreach ($this->distributeFields as $field) {
            // Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $this->roundingDelta[$field] = 0.0000001;
        }
        return $this;
    }

    /**
     * Distribute process
     *
     * @param QuoteItemDiscount $quoteItemDiscount
     * @return QuoteItemDiscount
     */
    private function distributeProcess($quoteItemDiscount)
    {
        $childrenDiscount = [];
        $item = $quoteItemDiscount->getItem();
        $parentBaseRowTotal = $this->processor->getTotalItemBasePrice($item);
        foreach ($item->getChildren() as $child) {
            $ratio = $this->processor->getTotalItemBasePrice($child) / $parentBaseRowTotal;

            /** @var QuoteItemDiscount $quoteItemChildDiscount */
            $quoteItemChildDiscount = $this->quoteItemDiscountFactory->create();
            foreach ($this->distributeFields as $field) {
                $itemValue = $quoteItemDiscount->{$this->generateMethodName($field, 'get')}();
                if (!$itemValue) {
                    continue;
                }

                $value = $itemValue * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $this->roundingDelta[$field]);
                $this->roundingDelta[$field] = $this->roundingDelta[$field] + $value - $roundedValue;

                $quoteItemChildDiscount->{$this->generateMethodName($field, 'set')}($roundedValue);
            }
            if ($quoteItemChildDiscount->getAmount()) {
                $quoteItemChildDiscount->setItem($child);
                $childrenDiscount[] = $quoteItemChildDiscount;
            }
        }

        $quoteItemDiscount
            ->setAmount(0)
            ->setBaseAmount(0)
            ->setChildren($childrenDiscount);

        return $quoteItemDiscount;
    }

    /**
     * Generate method name
     *
     * @param string $field
     * @param string $prefix
     * @return string
     */
    private function generateMethodName($field, $prefix)
    {
        return $prefix . $this->simpleDataObjectConverter->snakeCaseToUpperCamelCase($field);
    }
}
