<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount\Items;

use Aheadworks\Ctq\Model\Metadata\Quote\Item\DiscountFactory as QuoteItemDiscountFactory;
use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\DiscountCalculatorInterface;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item\Processor;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class AbstractItemCalculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount\Items
 */
abstract class AbstractItemCalculator
{
    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var QuoteItemDiscountFactory
     */
    protected $quoteItemDiscountFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @param MetadataFactory $metadataFactory
     * @param QuoteItemDiscountFactory $quoteItemDiscountFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param Processor $processor
     */
    public function __construct(
        MetadataFactory $metadataFactory,
        QuoteItemDiscountFactory $quoteItemDiscountFactory,
        PriceCurrencyInterface $priceCurrency,
        Processor $processor
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->quoteItemDiscountFactory = $quoteItemDiscountFactory;
        $this->priceCurrency = $priceCurrency;
        $this->processor = $processor;
    }

    /**
     * Init
     *
     * @param AbstractItem[] $items
     * @param float $amount
     */
    public function init($items, $amount)
    {
        $this->metadata = $this->metadataFactory->create();
        $this
            ->calculateTotalAmount($items)
            ->calculateAvailableAmount($amount);
    }

    /**
     * Calculate item amount
     *
     * @param AbstractItem $item
     * @return QuoteItemDiscount
     */
    public function calculateItemAmount($item)
    {
        $itemPrice = $this->processor->getTotalItemPrice($item);
        $baseItemPrice = $this->processor->getTotalItemBasePrice($item);

        $itemCalculateType = $item->getAwCtqCalculateType();
        $itemAmount = $this->metadata->getAvailableAmountLeft();
        $itemBaseAmount = $this->metadata->getBaseAvailableAmountLeft();
        if ($this->metadata->getItemsCount() > 1) {
            $rateForItem = $baseItemPrice / $this->metadata->getBaseItemsTotal();
            $itemBaseAmount = $this->metadata->getBaseAvailableAmount() * $rateForItem;

            $rateForItem = $itemPrice / $this->metadata->getItemsTotal();
            $itemAmount = $this->metadata->getAvailableAmount() * $rateForItem;

            $this->metadata->setItemsCount($this->metadata->getItemsCount() - 1);
        }

        if ($itemCalculateType == DiscountCalculatorInterface::CALCULATE_PER_ITEM) {
            $itemAmount = $itemPrice * $item->getAwCtqPercent() / 100;
            $itemBaseAmount = $baseItemPrice * $item->getAwCtqPercent() / 100;
        } elseif ($itemCalculateType == DiscountCalculatorInterface::CALCULATE_RESET) {
            $itemAmount = 0;
            $itemBaseAmount = 0;
        }

        $amount = min($itemAmount, $itemPrice);
        $baseAmount = min($itemBaseAmount, $baseItemPrice);

        $this->metadata
            ->setUsedAmount($this->metadata->getUsedAmount() + $amount)
            ->setBaseUsedAmount($this->metadata->getBaseUsedAmount() + $baseAmount);

        $percent = 100 - ($baseItemPrice - $amount) / $baseItemPrice * 100;

        /** @var QuoteItemDiscount $quoteItemDiscount */
        $quoteItemDiscount = $this->quoteItemDiscountFactory->create();
        $quoteItemDiscount
            ->setPercent($this->priceCurrency->round($percent))
            ->setAmount($amount)
            ->setBaseAmount($baseAmount)
            ->setItem($item);

        return $quoteItemDiscount;
    }

    /**
     * Calculate total amount
     *
     * @param AbstractItem[] $items
     * @return $this
     */
    protected function calculateTotalAmount($items)
    {
        $totalAmount = $itemsCount = 0;
        foreach ($items as $item) {
            if ($item->getAwCtqCalculateType() != DiscountCalculatorInterface::CALCULATE_RESET) {
                $totalAmount += $this->processor->getTotalItemBasePrice($item);
                $itemsCount++;
            }
        }

        $this->metadata
            ->setBaseItemsTotal($totalAmount)
            ->setItemsTotal($this->priceCurrency->convertAndRound($totalAmount))
            ->setItemsCount($itemsCount);

        return $this;
    }

    /**
     * Calculate available amount
     *
     * @param float $amount
     * @return $this
     */
    abstract protected function calculateAvailableAmount($amount);
}
