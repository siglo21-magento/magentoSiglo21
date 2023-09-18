<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Api\Data;

interface DocumentStatusInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const SAP_STATUS = 'sap_status';
    const SAP_DOC_ENTRY = 'sap_doc_entry';
    const SAP_RESULT = 'sap_result';
    const SAP = 'sap';
    const DOCUMENTSTATUS_ID = 'documentstatus_id';
    const PARENT_ID = 'parent_id';
    const TYPE = 'type';

    /**
     * Get documentstatus_id
     * @return string|null
     */
    public function getDocumentstatusId();

    /**
     * Set documentstatus_id
     * @param string $documentstatusId
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setDocumentstatusId($documentstatusId);

    /**
     * Get sap
     * @return string|null
     */
    public function getSap();

    /**
     * Set sap
     * @param string $sap
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSap($sap);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\SAP\Api\Data\DocumentStatusExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\SAP\Api\Data\DocumentStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\SAP\Api\Data\DocumentStatusExtensionInterface $extensionAttributes
    );

    /**
     * Get sap_doc_entry
     * @return string|null
     */
    public function getSapDocEntry();

    /**
     * Set sap_doc_entry
     * @param string $sapDocEntry
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSapDocEntry($sapDocEntry);

    /**
     * Get sap_status
     * @return string|null
     */
    public function getSapStatus();

    /**
     * Set sap_status
     * @param string $sapStatus
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSapStatus($sapStatus);

    /**
     * Get sap_result
     * @return string|null
     */
    public function getSapResult();

    /**
     * Set sap_result
     * @param string $sapResult
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setSapResult($sapResult);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setType($type);

    /**
     * Get parent_id
     * @return string|null
     */
    public function getParentId();

    /**
     * Set parent_id
     * @param string $parentId
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface
     */
    public function setParentId($parentId);
}

