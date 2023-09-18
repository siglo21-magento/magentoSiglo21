<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Total\Quote;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator as DiscountCalculator;
use Aheadworks\Ctq\Model\Quote\Discount\ItemsApplier;
use Magento\Quote\Model\Quote\Item;

/**
 * Class CtqDiscount
 *
 * @package Aheadworks\Ctq\Model\Total\Quote
 */
class CtqDiscount extends AbstractTotal
{
    /**
     * @var DiscountCalculator
     */
    private $discountCalculator;

    /**
     * @var ItemsApplier
     */
    private $itemsApplier;

    /**
     * @var bool
     */
    private $isFirstTimeResetRun = true;

    /**
     * @param DiscountCalculator $discountCalculator
     * @param ItemsApplier $itemsApplier
     */
    public function __construct(
        DiscountCalculator $discountCalculator,
        ItemsApplier $itemsApplier
    ) {
        $this->setCode('aw_ctq');
        $this->discountCalculator = $discountCalculator;
        $this->itemsApplier = $itemsApplier;
    }

    /**
     * @inheritdoc
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();
        $this->reset($quote, $address, $items);

        if (!count($items)) {
            return $this;
        }

        $quoteDiscount = $this->discountCalculator->calculate($items, $address, $quote);
        if (!$quoteDiscount->isDiscountAvailable()) {
            $this->reset($quote, $address, $items, true);
            return $this;
        }

        $this->itemsApplier->apply($items, $quoteDiscount);

        $address
            ->setAwCtqAmount($total->getAwCtqAmount())
            ->setBaseAwCtqAmount($total->getBaseAwCtqAmount());

        $this
            ->_addAmount(-$quoteDiscount->getAmount())
            ->_addBaseAmount(-$quoteDiscount->getBaseAmount());

        $total
            ->setSubtotalWithDiscount($total->getSubtotalWithDiscount() + $total->getAwCtqAmount())
            ->setBaseSubtotalWithDiscount($total->getBaseSubtotalWithDiscount() + $total->getBaseAwCtqAmount());

        $quote
            ->setAwCtqAmount($total->getAwCtqAmount())
            ->setBaseAwCtqAmount($total->getBaseAwCtqAmount());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fetch(Quote $quote, Total $total)
    {
        $this->setCode('aw_ctq');
        $amount = $total->getAwCtqAmount();
        if ($amount != 0) {
            return [
                'code' => $this->getCode(),
                'title' => __('Negotiated Discount'),
                'value' => $amount
            ];
        }

        return null;
    }

    /**
     * Reset totals
     *
     * @param Quote $quote
     * @param AddressInterface $address
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     * @param bool $reset
     * @return $this
     */
    protected function reset(Quote $quote, AddressInterface $address, $items, $reset = false)
    {
        if ($this->isFirstTimeResetRun || $reset) {
            $this
                ->_addAmount(0)
                ->_addBaseAmount(0);

            $quote
                ->setAwCtqAmount(0)
                ->setBaseAwCtqAmount(0);

            $address
                ->setAwCtqAmount(0)
                ->setBaseAwCtqAmount(0);

            /** @var Item $item */
            foreach ($items as $item) {
                $this->itemsApplier->reset($item);
            }

            $this->isFirstTimeResetRun = false;
        }
        return $this;
    }
}
