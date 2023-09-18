<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceList\Model;

use Aventi\PriceList\Api\Data\PriceListInterface;
use Aventi\PriceList\Api\Data\PriceListInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class PriceList extends \Magento\Framework\Model\AbstractModel
{

    protected $pricelistDataFactory;

    protected $_eventPrefix = 'aventi_pricelist_pricelist';
    protected $dataObjectHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PriceListInterfaceFactory $pricelistDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\PriceList\Model\ResourceModel\PriceList $resource
     * @param \Aventi\PriceList\Model\ResourceModel\PriceList\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PriceListInterfaceFactory $pricelistDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\PriceList\Model\ResourceModel\PriceList $resource,
        \Aventi\PriceList\Model\ResourceModel\PriceList\Collection $resourceCollection,
        array $data = []
    ) {
        $this->pricelistDataFactory = $pricelistDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve pricelist model with pricelist data
     * @return PriceListInterface
     */
    public function getDataModel()
    {
        $pricelistData = $this->getData();
        
        $pricelistDataObject = $this->pricelistDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $pricelistDataObject,
            $pricelistData,
            PriceListInterface::class
        );
        
        return $pricelistDataObject;
    }
}

