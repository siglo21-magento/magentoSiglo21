<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Api\Data;

interface ItemStatusInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const LINE_SAP = 'line_sap';
    const ITEMSTATUS_ID = 'itemstatus_id';
    const ITEM_ID = 'item_id';
    const BASE_ENTRY = 'base_entry';
    const BASE_TYPE = 'base_type';

    /**
     * Get itemstatus_id
     * @return string|null
     */
    public function getItemstatusId();

    /**
     * Set itemstatus_id
     * @param string $itemstatusId
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setItemstatusId($itemstatusId);

    /**
     * Get line_sap
     * @return string|null
     */
    public function getLineSap();

    /**
     * Set line_sap
     * @param string $lineSap
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setLineSap($lineSap);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\SAP\Api\Data\ItemStatusExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\SAP\Api\Data\ItemStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\SAP\Api\Data\ItemStatusExtensionInterface $extensionAttributes
    );

    /**
     * Get base_entry
     * @return string|null
     */
    public function getBaseEntry();

    /**
     * Set base_entry
     * @param string $baseEntry
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setBaseEntry($baseEntry);

    /**
     * Get base_type
     * @return string|null
     */
    public function getBaseType();

    /**
     * Set base_type
     * @param string $baseType
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setBaseType($baseType);

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param string $itemId
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface
     */
    public function setItemId($itemId);
}
