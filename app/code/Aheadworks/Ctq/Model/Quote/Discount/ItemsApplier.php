<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Discount;

use Aheadworks\Ctq\Model\Metadata\Quote\Discount as QuoteDiscount;
use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class ItemsApplier
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount
 */
class ItemsApplier
{
    /**
     * Reset negotiated discount data in item
     *
     * @param CartItemInterface|AbstractItem $item
     * @return $this
     */
    public function reset($item)
    {
        $item
            ->setAwCtqAmount(0)
            ->setBaseAwCtqAmount(0);

        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            foreach ($item->getChildren() as $child) {
                $child
                    ->setAwCtqAmount(0)
                    ->setBaseAwCtqAmount(0);
            }
        }

        return $this;
    }

    /**
     * Apply negotiated discount by items
     *
     * @param CartItemInterface[]|AbstractItem[] $items
     * @param QuoteDiscount $quoteDiscount
     * @return void
     */
    public function apply($items, $quoteDiscount)
    {
        foreach ($items as $item) {
            // To determine the child item discount, we calculate the parent
            if ($item->getParentItem()) {
                continue;
            }
            $item->setAwCtqAmount(0);
            $item->setBaseAwCtqAmount(0);
            $this->processApply($item, $quoteDiscount);
        }
    }

    /**
     * Apply negotiated discount by items
     *
     * @param CartItemInterface|AbstractItem $item
     * @param QuoteDiscount $quoteDiscount
     * @return $this
     */
    private function processApply($item, $quoteDiscount)
    {
        /** @var QuoteItemDiscount $itemDiscount */
        if ($itemDiscount = $this->getItemDiscountBySku($item->getSku(), $quoteDiscount->getItems())) {
            $item->setAwCtqAmount($itemDiscount->getAmount());
            $item->setBaseAwCtqAmount($itemDiscount->getBaseAmount());
            $item->setAwCtqPercent($itemDiscount->getPercent());

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if ($childDiscount = $this->getItemDiscountBySku($child->getSku(), $itemDiscount->getChildren())) {
                        $child->setAwCtqAmount($childDiscount->getAmount());
                        $child->setBaseAwCtqAmount($childDiscount->getBaseAmount());
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Retrieve item discount by sku
     *
     * @param string $sku
     * @param QuoteItemDiscount[] $items
     * @return QuoteItemDiscount|bool
     */
    private function getItemDiscountBySku($sku, $items)
    {
        foreach ($items as $item) {
            if ($item->getItem()->getSku() == $sku) {
                return $item;
            }
        }

        return false;
    }
}
