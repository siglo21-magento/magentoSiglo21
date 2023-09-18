<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

/**
 * Class Validator
 *
 * @package Aheadworks\Ctq\Model\Cart
 */
class Validator
{
    /**
     * Check is quote item valid
     *
     * @param QuoteItem $item
     * @return bool
     */
    public function isItemValid($item)
    {
        try {
            /** @var ProductInterface $product */
            $product = $item->getProduct();
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        return $product && $this->isValidProduct($product);
    }

    /**
     * Check is product valid
     *
     * @param ProductInterface $product
     * @return bool
     */
    private function isValidProduct(ProductInterface $product): bool
    {
        return (int)$product->getStatus() !== ProductStatus::STATUS_DISABLED;
    }
}
