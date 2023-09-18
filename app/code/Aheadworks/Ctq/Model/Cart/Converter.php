<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart;

use Aheadworks\Ctq\Api\Data\CartItemInterface;
use Aheadworks\Ctq\Model\Converter\DataObjectConverter;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Quote\Model\Quote\Item\OptionFactory;

/**
 * Class Converter
 * @package Aheadworks\Ctq\Model\Cart
 */
class Converter
{
    /**
     * Default sort order
     */
    const DEFAULT_SORT_ORDER = 32000;

    /**
     * @var CartInterfaceFactory
     */
    private $cartFactory;

    /**
     * @var CartItemInterfaceFactory
     */
    private $cartItemFactory;

    /**
     * @var OptionFactory
     */
    private $optionFactory;

    /**
     * @var ProductInterfaceFactory
     */
    private $productFactory;

    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param CartInterfaceFactory $cartFactory
     * @param CartItemInterfaceFactory $cartItemFactory
     * @param ProductInterfaceFactory $productFactory
     * @param OptionFactory $optionFactory
     * @param DataObjectConverter $dataObjectConverter
     * @param Validator $validator
     */
    public function __construct(
        CartInterfaceFactory $cartFactory,
        CartItemInterfaceFactory $cartItemFactory,
        ProductInterfaceFactory $productFactory,
        OptionFactory $optionFactory,
        DataObjectConverter $dataObjectConverter,
        Validator $validator
    ) {
        $this->cartFactory = $cartFactory;
        $this->cartItemFactory = $cartItemFactory;
        $this->productFactory = $productFactory;
        $this->optionFactory = $optionFactory;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->validator = $validator;
    }

    /**
     * Convert cart object to array
     *
     * @param CartInterface $cart
     * @return array
     */
    public function toArray($cart)
    {
        $cartArray['quote'] = $this->dataObjectConverter->convertObjectToFlatArray($cart);
        $cartArray['billing_address'] =  $cart->getBillingAddress()
            ? $this->dataObjectConverter->convertObjectToFlatArray($cart->getBillingAddress())
            : [];
        $cartArray['shipping_address'] = $cart->getShippingAddress()
            ? $this->dataObjectConverter->convertObjectToFlatArray($cart->getShippingAddress())
            : [];

        $cartArray['items'] = [];
        foreach ($cart->getItemsCollection() as $item) {
            if ($item->isDeleted()) {
                continue;
            }
            $itemData = $this->dataObjectConverter->convertObjectToFlatArray($item);

            $itemOptions = [];
            foreach ($item->getOptions() as $option) {
                $product = $option->getProduct();
                $itemOption = $this->dataObjectConverter->convertObjectToFlatArray($option);
                $itemOption['product'] = $product
                    ? $this->dataObjectConverter->convertObjectToFlatArray($product)
                    : [];
                $itemOptions[] = $itemOption;
            }
            $itemData['options'] = $itemOptions;
            $cartArray['items'][] = $itemData;
        }

        return $cartArray;
    }

    /**
     * Convert cart array to object
     *
     * @param array $cartArray
     * @param bool $isNew
     * @return CartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toObject($cartArray, $isNew = false)
    {
        /** @var Quote|CartInterface $cart */
        $cart = $this->cartFactory->create();

        $cart->setData($cartArray['quote']);
        $cart->setAwCtqQuoteIsChanged(false);
        $cart->removeAllItems();
        $itemsCollection = $cart->getItemsCollection();
        if ($cartArray['billing_address']) {
            $cart->getBillingAddress()->setData($cartArray['billing_address']);
            if ($isNew) {
                $cart->getBillingAddress()->setId(null);
            }
        }
        if ($cartArray['shipping_address']) {
            $cart->getShippingAddress()->setData($cartArray['shipping_address']);
            if ($isNew) {
                $cart->getShippingAddress()->setId(null);
            }
        }
        if ($isNew) {
            $cart->setId(null);
        }

        $itemIdsInCart = $itemsCollection->getAllIds();

        $this->sortItems($cartArray['items']);
        $cartItems = [];
        foreach ($cartArray['items'] as $item) {
            $isRemove = $isNew;
            /** @var Quote\Item $itemObject */
            $itemObject = $this->cartItemFactory->create();
            $itemObject->setData($item);
            $itemObject->setQuote($cart);

            if (!$this->validator->isItemValid($itemObject)) {
                $itemObject->isDeleted(true);
                $cartItems[$itemObject->getItemId()] = $itemObject;

                // Mark as removed parent item of invalid child
                $parentItemId = $itemObject->getParentItemId();
                if ($parentItemId || isset($cartItems[$parentItemId])) {
                    $cartItems[$parentItemId]->isDeleted(true);
                }

                $cart->setAwCtqQuoteIsChanged(true);
                continue;
            }
            $itemObject->setSku($item['sku']);
            $itemObject->setTaxCalculationPrice(null);

            $itemId = $itemObject->getItemId();
            $parentItemId = $itemObject->getParentItemId();

            if (in_array($itemId, $itemIdsInCart)) {
                $itemsCollection->removeItemByKey($itemId);
            } else {
                $isRemove = true;
            }
            if ($isRemove) {
                $itemObject->setItemId(null);
                $itemObject->setParentItemId(null);
            }

            if (isset($cartItems[$parentItemId])) {
                $parentItem = $cartItems[$parentItemId];
                $itemObject->setParentItem($parentItem);

                // Mark item as deleted if parent item is deleted
                $itemObject->isDeleted($parentItem->isDeleted());
            }

            foreach ($item['options'] as $option) {
                $productObject = $this->productFactory->create();
                $productObject->setData($option['product']);

                $optionObject = $this->optionFactory->create();
                $optionObject->setData($option);
                $optionObject->setProduct($productObject);

                if ($isRemove) {
                    $optionObject->setOptionId(null);
                }

                $itemObject->addOption($optionObject);
            }
            $itemObject->setData('qty_options', null);
            $cartItems[$itemId] = $itemObject;
        }

        // Another loop to avoid disabled/deleted child item
        foreach ($cartItems as $cartItem) {
            if ($cartItem->isDeleted()) {
                foreach ((array)$cartItem->getOptions() as $option) {
                    $option->isDeleted(true);
                }
            }
            $itemsCollection->addItem($cartItem);
        }

        return $cart;
    }

    /**
     * Sorting items according to sort order
     *
     * @param array $items
     */
    private function sortItems(array &$items)
    {
        usort($items, function ($a, $b) {
            $sortOrderA = isset($a[CartItemInterface::AW_CTQ_SORT_ORDER])
                ? $a[CartItemInterface::AW_CTQ_SORT_ORDER]
                : self::DEFAULT_SORT_ORDER;
            $sortOrderB = isset($b[CartItemInterface::AW_CTQ_SORT_ORDER])
                ? $b[CartItemInterface::AW_CTQ_SORT_ORDER]
                : self::DEFAULT_SORT_ORDER;

            return $sortOrderA <=> $sortOrderB;
        });
    }
}
