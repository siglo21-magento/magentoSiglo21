<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Model\Order\Item;

/**
 * Class ConvertQuoteItemToOrderItemPlugin
 *
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Item
 */
class ConvertQuoteItemToOrderItemPlugin
{
    /**
     * Convert quote item data to order item data
     *
     * @param ToOrderItem $subject
     * @param callable $proceed
     * @param AbstractItem $item
     * @param array $additional
     * @return Item
     */
    public function aroundConvert(
        ToOrderItem $subject,
        callable $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);

        $orderItem->setAwCtqPercent($item->getAwCtqPercent());
        $orderItem->setAwCtqAmount($item->getAwCtqAmount());
        $orderItem->setBaseAwCtqAmount($item->getBaseAwCtqAmount());

        return $orderItem;
    }
}
