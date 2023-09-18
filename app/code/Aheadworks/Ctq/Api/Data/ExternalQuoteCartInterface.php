<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

/**
 * This interface is used for web api only
 *
 * Interface ExternalQuoteCartInterface
 * @api
 */
interface ExternalQuoteCartInterface extends QuoteCartInterface
{
    /**
     * Get quote
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getQuote();

    /**
     * Set quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return $this
     */
    public function setQuote($quote);

    /**
     * Get items
     *
     * @return \Magento\Quote\Api\Data\CartItemInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     * @return $this
     */
    public function setItems($items);

    /**
     * Get shipping address
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function getShippingAddress();

    /**
     * Set shipping address
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Get billing address
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set billing address
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @return $this
     */
    public function setBillingAddress($billingAddress);
}
