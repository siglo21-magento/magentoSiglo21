<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model;

use Aventi\SAP\Api\Data\ItemStatusInterface;
use Aventi\SAP\Api\Data\ItemStatusInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class ItemStatus extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $itemstatusDataFactory;

    protected $_eventPrefix = 'aventi_sap_itemstatus';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ItemStatusInterfaceFactory $itemstatusDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\SAP\Model\ResourceModel\ItemStatus $resource
     * @param \Aventi\SAP\Model\ResourceModel\ItemStatus\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ItemStatusInterfaceFactory $itemstatusDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\SAP\Model\ResourceModel\ItemStatus $resource,
        \Aventi\SAP\Model\ResourceModel\ItemStatus\Collection $resourceCollection,
        array $data = []
    ) {
        $this->itemstatusDataFactory = $itemstatusDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve itemstatus model with itemstatus data
     * @return ItemStatusInterface
     */
    public function getDataModel()
    {
        $itemstatusData = $this->getData();

        $itemstatusDataObject = $this->itemstatusDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemstatusDataObject,
            $itemstatusData,
            ItemStatusInterface::class
        );

        return $itemstatusDataObject;
    }
}
