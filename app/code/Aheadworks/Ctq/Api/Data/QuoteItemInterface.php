<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface QuoteItemInterface
 * @api
 * @todo add this interface for quote items update, see Quote SaveHandler
 */
interface QuoteItemInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ITEM_ID = 'item_id';
    const QTY = 'qty';
    const PRODUCT_OPTION = 'product_option';
    /**#@-*/

    /**
     * Get item ID
     *
     * @return int
     */
    public function getItemId();

    /**
     * Set item ID
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Get qty
     *
     * @return float
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get product option
     *
     * @return \Magento\Quote\Api\Data\ProductOptionInterface|null
     */
    public function getProductOption();

    /**
     * Sets product option
     *
     * @param \Magento\Quote\Api\Data\ProductOptionInterface $productOption
     * @return $this
     */
    public function setProductOption(\Magento\Quote\Api\Data\ProductOptionInterface $productOption);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ctq\Api\Data\QuoteItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\QuoteItemExtensionInterface $extensionAttributes
    );
}
