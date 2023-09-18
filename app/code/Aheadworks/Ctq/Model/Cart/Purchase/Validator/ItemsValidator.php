<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart\Purchase\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;

/**
 * Class ItemsValidator
 * @package Aheadworks\Ctq\Model\Cart\Purchase\Validator
 */
class ItemsValidator extends AbstractValidator
{
    /**
     * Validate cart items
     *
     * @param CartInterface|Quote $cart
     * @return bool
     */
    public function isValid($cart)
    {
        $quote = $cart->getExtensionAttributes()->getAwCtqQuote();
        $quoteCart = $quote->getCart();

        $quoteCartItems = $quoteCart->getItems();
        $cartItems = $cart instanceof Quote
            ? $cart->getAllItems()
            : $cart->getItems();

        if (count($quoteCartItems) != count($cartItems)) {
            $this->_addMessages(['You can\'t change the count of products.']);
        }

        foreach ($quoteCartItems as $quoteCartItem) {
            $quoteCartItemId = $quoteCartItem['item_id'];
            $quoteCartItemQty = $quoteCartItem['qty'];
            $quoteCartItemName = $quoteCartItem['name'];

            $cartItem = $this->getCartItemById($quoteCartItemId, $cartItems);
            if (!$cartItem) {
                $this->_addMessages(['You can\'t remove products from the cart.']);
            } else {
                if ($quoteCartItemQty != $cartItem->getQty()) {
                    $this->_addMessages([sprintf('Qty doesn\'t match, product name is %s.', $quoteCartItemName)]);
                }

                foreach ($quoteCartItem['options'] as $quoteCartItemOption) {
                    $quoteCartItemOptionId = $quoteCartItemOption['option_id'];
                    $quoteCartItemOptionValue = $quoteCartItemOption['value'];
                    $quoteCartItemOptionCode = $quoteCartItemOption['code'];

                    $cartOptions = $cartItem instanceof Item
                        ? $cartItem->getOptions()
                        : $cartItem->getProductOption();

                    $cartItemOption = $this->getCartItemOptionById($quoteCartItemOptionId, $cartOptions);
                    if (!$cartItemOption) {
                        $this->_addMessages(['You can\'t remove product option.']);
                    } elseif ($quoteCartItemOptionValue != $cartItemOption->getValue()) {
                        $this->_addMessages([
                            sprintf(
                                'Option value doesn\'t match, product name is %s, option code %s.',
                                $quoteCartItemName,
                                $quoteCartItemOptionCode
                            )
                        ]);
                    }
                }
            }
        }

        return empty($this->getMessages());
    }

    /**
     * Retrieve item by id
     *
     * @param int $itemId
     * @param CartItemInterface[] $items
     * @return CartItemInterface|Item|null
     */
    private function getCartItemById($itemId, $items)
    {
        foreach ($items as $item) {
            if ($itemId == $item->getItemId()) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Retrieve item option by id
     *
     * @param int $optionId
     * @param Option[] $options
     * @return Option|null
     */
    private function getCartItemOptionById($optionId, $options)
    {
        foreach ($options as $option) {
            if ($optionId == $option->getOptionId()) {
                return $option;
            }
        }
        return null;
    }
}
