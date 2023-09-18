<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\ShowInventory\Block\Product;

class Inventory extends \Magento\Framework\View\Element\Template
{    

    private $logger;    

    private $getSourceItemsBySkuInterface;

    private $sourceRepositoryInterface;

    private $sourceCollectionFactory;

    private $helper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySkuInterface,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepositoryInterface,
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory,
        \Aventi\ShowInventory\Helper\Data $helper,
        array $data = [],        
        \Psr\Log\LoggerInterface $logger        
    ) {        
        $this->logger = $logger;        
        $this->getSourceItemsBySkuInterface = $getSourceItemsBySkuInterface;
        $this->sourceRepositoryInterface = $sourceRepositoryInterface;
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        $this->helper = $helper;
        parent::__construct($context, $data);        
    }    

    /**
     * @return string
     */
    public function displaySourceInventoryByCity($sku)
    {                        
        return $this->helper->displaySourceInventoryByCity($sku);
    }

    /**
     * @return string
     */
    public function displaySourceInventoryListMaterial($sku)
    {                        
        return $this->helper->displaySourceInventoryListMaterial($sku);
    }
    
}

