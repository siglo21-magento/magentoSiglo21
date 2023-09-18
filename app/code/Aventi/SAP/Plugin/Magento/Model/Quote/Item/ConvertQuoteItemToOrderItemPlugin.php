<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aventi\SAP\Plugin\Magento\Model\Quote\Item;

use Aventi\SAP\Api\ItemStatusRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Model\Order\Item;
use Psr\Log\LoggerInterface;

/**
 * Class ConvertQuoteItemToOrderItemPlugin
 *
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Item
 */
class ConvertQuoteItemToOrderItemPlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ItemStatusRepositoryInterface
     */
    private $itemStatusRepositoryInterface;

    public function __construct(
        LoggerInterface $logger,
        ItemStatusRepositoryInterface $itemStatusRepositoryInterface
    ) {
        $this->logger = $logger;
        $this->itemStatusRepositoryInterface = $itemStatusRepositoryInterface;
    }

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
        try {
            $itemStatus = $this->itemStatusRepositoryInterface->getByItemId($item->getId());
            $orderItem->setLineSap($itemStatus->getLineSap());
            $orderItem->setBaseEntry($itemStatus->getBaseEntry());
            $orderItem->setBaseType($itemStatus->getBaseType());
        } catch (LocalizedException $e) {
            $this->logger->debug("ERROR TO GET ITEM STATUS: " . $e->getMessage());
        }
        return $orderItem;
    }
}
