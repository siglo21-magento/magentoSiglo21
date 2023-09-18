<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model;

use Aventi\SAP\Api\Data\DocumentStatusInterface;
use Aventi\SAP\Api\Data\DocumentStatusInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class DocumentStatus extends \Magento\Framework\Model\AbstractModel
{

    protected $documentstatusDataFactory;

    protected $_eventPrefix = 'aventi_sap_documentstatus';
    protected $dataObjectHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DocumentStatusInterfaceFactory $documentstatusDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\SAP\Model\ResourceModel\DocumentStatus $resource
     * @param \Aventi\SAP\Model\ResourceModel\DocumentStatus\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        DocumentStatusInterfaceFactory $documentstatusDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\SAP\Model\ResourceModel\DocumentStatus $resource,
        \Aventi\SAP\Model\ResourceModel\DocumentStatus\Collection $resourceCollection,
        array $data = []
    ) {
        $this->documentstatusDataFactory = $documentstatusDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve documentstatus model with documentstatus data
     * @return DocumentStatusInterface
     */
    public function getDataModel()
    {
        $documentstatusData = $this->getData();
        
        $documentstatusDataObject = $this->documentstatusDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $documentstatusDataObject,
            $documentstatusData,
            DocumentStatusInterface::class
        );
        
        return $documentstatusDataObject;
    }
}

