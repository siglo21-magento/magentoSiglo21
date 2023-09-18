<?php

namespace Aventi\UpdateOrderStatus\Model;

use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class UpdateStock
{
    const DEFAULT_SOURCE = 'default';

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockItemRegistry;

    /**
     * @var SourceItemInterfaceFactory
     */
    protected $itemInterfaceFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $_productCollectionFactory;

    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSave;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param SourceItemInterfaceFactory $itemInterfaceFactory
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemRegistry
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $itemInterfaceFactory,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSave,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemRegistry,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->itemInterfaceFactory = $itemInterfaceFactory;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->stockItemRegistry = $stockItemRegistry;
        $this->logger = $logger;
    }

    /**
     * @param $sku
     * @param $qty
     * @return void
     */
    public function setDefaultSource($sku, $qty)
    {
        try {
            $sourceItem = $this->itemInterfaceFactory->create();
            $sourceItem->setSourceCode('CDBOSQUE');
            $sourceItem->setSku($sku);
            $sourceItem->setQuantity($qty);
            $sourceItem->setStatus(1);
            $this->sourceItemsSave->execute([$sourceItem]);
        } catch (\Exception $e) {
            $this->logger->debug('There was an error saving the product stock: ' . $e->getMessage());
        }
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setDefaultStock()
    {
        $collection = $this->_productCollectionFactory->create();
        if ($collection->count() > 0) {
            foreach ($collection as $product) {
                $this->setDefaultSource($product->getSku(), 5000);
            }
        }
    }

    /**
     * @param $productId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function getStockItem($productId)
    {
        return $this->stockItemRegistry->getStockItemBySku($productId);
    }

}

