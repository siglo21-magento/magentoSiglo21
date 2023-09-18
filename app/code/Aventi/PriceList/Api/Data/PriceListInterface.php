<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceList\Api\Data;

interface PriceListInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const PRICE = 'price';
    const PRICELIST_ID = 'pricelist_id';
    const GROUP = 'group';
    const SKU = 'sku';

    /**
     * Get pricelist_id
     * @return string|null
     */
    public function getPricelistId();

    /**
     * Set pricelist_id
     * @param string $pricelistId
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setPricelistId($pricelistId);

    /**
     * Get sku
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setSku($sku);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceList\Api\Data\PriceListExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceList\Api\Data\PriceListExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceList\Api\Data\PriceListExtensionInterface $extensionAttributes
    );

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setPrice($price);

    /**
     * Get group
     * @return string|null
     */
    public function getGroup();

    /**
     * Set group
     * @param string $group
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setGroup($group);
}

