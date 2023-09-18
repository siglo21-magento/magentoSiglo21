<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model\Data;

use Aventi\SAP\Api\Data\ItemStatusInterface;

class ItemStatus extends \Magento\Framework\Api\AbstractExtensibleObject implements ItemStatusInterface
{

    /**
     * Get itemstatus_id
     * @return string|null
     */
    public function getItemstatusId()
    {
        return $this->_get(self::ITEMSTATUS_ID);
    }

    /**
     * Set itemstatus_id
     * @param string $itemstatusId
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setItemstatusId($itemstatusId)
    {
        return $this->setData(self::ITEMSTATUS_ID, $itemstatusId);
    }

    /**
     * Get line_sap
     * @return string|null
     */
    public function getLineSap()
    {
        return $this->_get(self::LINE_SAP);
    }

    /**
     * Set line_sap
     * @param string $lineSap
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setLineSap($lineSap)
    {
        return $this->setData(self::LINE_SAP, $lineSap);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\SAP\Api\Data\ItemStatusExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\SAP\Api\Data\ItemStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\SAP\Api\Data\ItemStatusExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get base_entry
     * @return string|null
     */
    public function getBaseEntry()
    {
        return $this->_get(self::BASE_ENTRY);
    }

    /**
     * Set base_entry
     * @param string $baseEntry
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setBaseEntry($baseEntry)
    {
        return $this->setData(self::BASE_ENTRY, $baseEntry);
    }

    /**
     * Get base_type
     * @return string|null
     */
    public function getBaseType()
    {
        return $this->_get(self::BASE_TYPE);
    }

    /**
     * Set base_type
     * @param string $baseType
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setBaseType($baseType)
    {
        return $this->setData(self::BASE_TYPE, $baseType);
    }

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * Set item_id
     * @param string $itemId
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }
}
