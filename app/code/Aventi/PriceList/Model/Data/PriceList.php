<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceList\Model\Data;

use Aventi\PriceList\Api\Data\PriceListInterface;

class PriceList extends \Magento\Framework\Api\AbstractExtensibleObject implements PriceListInterface
{

    /**
     * Get pricelist_id
     * @return string|null
     */
    public function getPricelistId()
    {
        return $this->_get(self::PRICELIST_ID);
    }

    /**
     * Set pricelist_id
     * @param string $pricelistId
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setPricelistId($pricelistId)
    {
        return $this->setData(self::PRICELIST_ID, $pricelistId);
    }

    /**
     * Get sku
     * @return string|null
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Set sku
     * @param string $sku
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceList\Api\Data\PriceListExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceList\Api\Data\PriceListExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceList\Api\Data\PriceListExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get price
     * @return string|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Set price
     * @param string $price
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get group
     * @return string|null
     */
    public function getGroup()
    {
        return $this->_get(self::GROUP);
    }

    /**
     * Set group
     * @param string $group
     * @return \Aventi\PriceList\Api\Data\PriceListInterface
     */
    public function setGroup($group)
    {
        return $this->setData(self::GROUP, $group);
    }
}

