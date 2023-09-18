<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model\Data;

use Aventi\SAP\Api\Data\DocumentStatusInterface;

class DocumentStatus extends \Magento\Framework\Api\AbstractExtensibleObject implements DocumentStatusInterface
{

    /**
     * Get documentstatus_id
     * @return string|null
     */
    public function getDocumentstatusId()
    {
        return $this->_get(self::DOCUMENTSTATUS_ID);
    }

    /**
     * Set documentstatus_id
     * @param string $documentstatusId
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setDocumentstatusId($documentstatusId)
    {
        return $this->setData(self::DOCUMENTSTATUS_ID, $documentstatusId);
    }

    /**
     * Get sap
     * @return string|null
     */
    public function getSap()
    {
        return $this->_get(self::SAP);
    }

    /**
     * Set sap
     * @param string $sap
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSap($sap)
    {
        return $this->setData(self::SAP, $sap);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\SAP\Api\Data\DocumentStatusExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\SAP\Api\Data\DocumentStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\SAP\Api\Data\DocumentStatusExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get sap_doc_entry
     * @return string|null
     */
    public function getSapDocEntry()
    {
        return $this->_get(self::SAP_DOC_ENTRY);
    }

    /**
     * Set sap_doc_entry
     * @param string $sapDocEntry
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSapDocEntry($sapDocEntry)
    {
        return $this->setData(self::SAP_DOC_ENTRY, $sapDocEntry);
    }

    /**
     * Get sap_status
     * @return string|null
     */
    public function getSapStatus()
    {
        return $this->_get(self::SAP_STATUS);
    }

    /**
     * Set sap_status
     * @param string $sapStatus
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSapStatus($sapStatus)
    {
        return $this->setData(self::SAP_STATUS, $sapStatus);
    }

    /**
     * Get sap_result
     * @return string|null
     */
    public function getSapResult()
    {
        return $this->_get(self::SAP_RESULT);
    }

    /**
     * Set sap_result
     * @param string $sapResult
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSapResult($sapResult)
    {
        return $this->setData(self::SAP_RESULT, $sapResult);
    }

    /**
     * Get type
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Set type
     * @param string $type
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get parent_id
     * @return string|null
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Set parent_id
     * @param string $parentId
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }
}

