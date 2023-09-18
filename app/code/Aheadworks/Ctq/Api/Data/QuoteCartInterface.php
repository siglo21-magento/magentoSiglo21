<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface QuoteCartInterface
 * @api
 */
interface QuoteCartInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const QUOTE = 'quote';
    const ITEMS = 'items';
    const SHIPPING_ADDRESS = 'shipping_address';
    const BILLING_ADDRESS = 'billing_address';
    /**#@-*/

    /**
     * Get quote
     *
     * @return string[]
     */
    public function getQuote();

    /**
     * Set quote
     *
     * @param string[] $quote
     * @return $this
     */
    public function setQuote($quote);

    /**
     * Get items
     *
     * @return string[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param string[] $items
     * @return $this
     */
    public function setItems($items);

    /**
     * Get shipping address
     *
     * @return string[]
     */
    public function getShippingAddress();

    /**
     * Set shipping address
     *
     * @param string[] $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Get billing address
     *
     * @return string[]
     */
    public function getBillingAddress();

    /**
     * Set billing address
     *
     * @param string[] $billingAddress
     * @return $this
     */
    public function setBillingAddress($billingAddress);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ctq\Api\Data\QuoteCartExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteCartExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\QuoteCartExtensionInterface $extensionAttributes
    );
}
