<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\History\Action\Type as ActionType;
use Aheadworks\Ctq\Model\Source\History\Action\Status as ActionStatus;

/**
 * Class ItemsBuilder
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
class ItemsBuilder implements BuilderInterface
{
    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @param HistoryActionInterfaceFactory $historyActionFactory
     */
    public function __construct(HistoryActionInterfaceFactory $historyActionFactory)
    {
        $this->historyActionFactory = $historyActionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function build($quote)
    {
        $historyActions = [];
        if ($quote->getOrigData(QuoteInterface::ID) === null) {
            return $historyActions;
        }

        $removedItems = $createdItems = [];

        /** @var QuoteCartInterface $oldCart */
        $oldCart = $quote->getOrigData(QuoteInterface::CART);
        $oldItems = $oldCart ? $oldCart->getItems() : [];
        $newItems = $quote->getCart()->getItems();

        foreach ($oldItems as $oldItem) {
            if ($oldItem['parent_item_id']) {
                continue;
            }
            $oldItemId = $oldItem['item_id'];

            $newItem = $this->getItemById($oldItemId, $newItems);
            if (!$newItem) {
                $removedItems[] = $oldItem;
            }
        }

        foreach ($newItems as $newItem) {
            if ($newItem['parent_item_id']) {
                continue;
            }
            $newItemId = $newItem['item_id'];
            $newItemProductId = $newItem['product_id'];

            $oldItem = $this->getItemById($newItemId, $oldItems);
            if (!$oldItem) {
                $createdItems[] = $newItem;
            } else {
                $productHistoryActions = array_merge(
                    $this->diffItemAttributes($newItem, $oldItem),
                    $this->diffItemOptions($newItem, $oldItem)
                );

                if ($productHistoryActions) {
                    /** @var HistoryActionInterface $historyAction */
                    $historyAction = $this->historyActionFactory->create();
                    $historyAction
                        ->setType(ActionType::PRODUCT)
                        ->setStatus(ActionStatus::UPDATED)
                        ->setValue($newItemProductId)
                        ->setActions($productHistoryActions);
                    $historyActions[] = $historyAction;
                }
            }
        }

        foreach ($removedItems as $removedItemKey => $removedItem) {
            $createdItem = $this->getItemById($removedItem['product_id'], $createdItems, 'product_id', true);
            if ($createdItem) {
                list($createdItemKey, $createdItem) = $createdItem;
                $newItemProductId = $createdItem['product_id'];
                /** @var HistoryActionInterface $historyAction */
                $historyAction = $this->historyActionFactory->create();
                $historyAction
                    ->setType(ActionType::PRODUCT)
                    ->setStatus(ActionStatus::RECONFIGURED)
                    ->setValue($newItemProductId);
                $historyActions[] = $historyAction;

                unset($removedItems[$removedItemKey]);
                unset($createdItems[$createdItemKey]);
            }
        }

        foreach ($removedItems as $removedItem) {
            $productId = $removedItem['product_id'];
            /** @var HistoryActionInterface $historyAction */
            $historyAction = $this->historyActionFactory->create();
            $historyAction
                ->setType(ActionType::PRODUCT)
                ->setStatus(ActionStatus::REMOVED)
                ->setValue($productId);
            $historyActions[] = $historyAction;
        }

        foreach ($createdItems as $createdItem) {
            $productId = $createdItem['product_id'];
            /** @var HistoryActionInterface $historyAction */
            $historyAction = $this->historyActionFactory->create();
            $historyAction
                ->setType(ActionType::PRODUCT)
                ->setStatus(ActionStatus::CREATED)
                ->setValue($productId);
            $historyActions[] = $historyAction;
        }

        return $historyActions;
    }

    /**
     * Find difference between new and old product attributes
     *
     * @param array $newItem
     * @param array $oldItem
     * @return HistoryActionInterface[]
     */
    private function diffItemAttributes($newItem, $oldItem)
    {
        $productHistoryActions = [];
        $newItemQty = $newItem['qty'];
        $oldItemQty = $oldItem['qty'];
        if ($newItemQty != $oldItemQty) {
            /** @var HistoryActionInterface $historyAction */
            $historyAction = $this->historyActionFactory->create();
            $historyAction
                ->setType(ActionType::PRODUCT_ATTRIBUTE_QTY)
                ->setStatus(ActionStatus::UPDATED)
                ->setOldValue($oldItemQty)
                ->setValue($newItemQty);
            $productHistoryActions[] = $historyAction;
        }
        return $productHistoryActions;
    }

    /**
     * Find difference between new and old product options
     *
     * @param array $newItem
     * @param array $oldItem
     * @return HistoryActionInterface[]
     */
    private function diffItemOptions($newItem, $oldItem)
    {
        $productHistoryActions = [];
        $newItemOptions = $newItem['options'];
        $oldItemOptions = $oldItem['options'];
        foreach ($newItemOptions as $newItemOption) {
            $newItemOptionId = $newItemOption['option_id'];
            $newItemOptionValue = $newItemOption['value'];
            $newItemOptionCode = $newItemOption['code'];

            $oldItemOption = $this->getItemOptionById($newItemOptionId, $oldItemOptions);
            if (!$oldItemOption) {
                /** @var HistoryActionInterface $historyAction */
                $historyAction = $this->historyActionFactory->create();
                $historyAction
                    ->setType(ActionType::PRODUCT_OPTION)
                    ->setStatus(ActionStatus::CREATED)
                    ->setName($newItemOptionCode)
                    ->setValue($newItemOptionValue);
                $productHistoryActions[] = $historyAction;
            } else {
                $oldItemOptionValue = $oldItemOption['value'];

                if ($newItemOptionValue != $oldItemOptionValue) {
                    /** @var HistoryActionInterface $historyAction */
                    $historyAction = $this->historyActionFactory->create();
                    $historyAction
                        ->setType(ActionType::PRODUCT_OPTION)
                        ->setStatus(ActionStatus::UPDATED)
                        ->setName($newItemOptionCode)
                        ->setOldValue($oldItemOptionValue)
                        ->setValue($newItemOptionValue);
                    $productHistoryActions[] = $historyAction;
                }
            }
        }
        return $productHistoryActions;
    }

    /**
     * Retrieve item by id
     *
     * @param int $itemId
     * @param array $items
     * @param string $fieldName
     * @param bool $withItemKey
     * @return array|null
     */
    private function getItemById($itemId, $items, $fieldName = 'item_id', $withItemKey = false)
    {
        foreach ($items as $itemKey => $item) {
            if ($itemId == $item[$fieldName]) {
                return $withItemKey ? [$itemKey, $item] : $item;
            }
        }
        return null;
    }

    /**
     * Retrieve item option by id
     *
     * @param int $optionId
     * @param array $options
     * @return array|null
     */
    private function getItemOptionById($optionId, $options)
    {
        foreach ($options as $option) {
            if ($optionId == $option['option_id']) {
                return $option;
            }
        }
        return null;
    }
}
