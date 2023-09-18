<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model\Sync;

use Aventi\SAP\Model\Check;
use Aventi\SAP\Model\Integration;
use Bcn\Component\Json\Exception\ReadingError;
use Bcn\Component\Json\Reader;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Validation\ValidationException;
use Magento\InventoryApi\Api\Data\SourceItemInterface;

class Stock extends Integration
{
    private $resTable = [
        'check' => 0,
        'fail' => 0,
        'new' => 0,
        'updated' => 0
    ];

    /**
     * @var \Aventi\SAP\Helper\Data
     */
    private $_data;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $_productRepository;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $_sourceRepository;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $_filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $_filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $_searchCriteriaBuilder;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface
     */
    private $_sourceItemRepositoryInterface;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    private $_sourceItemSave;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    private $_sourceItemInterfaceFactory;

    /**
     * @var Check
     */
    private $_check;

    private $lastItem;

    private $arrayItems = [];

    public function __construct(
        \Aventi\SAP\Helper\Attribute $attribute,
        \Aventi\SAP\Logger\Logger $logger,
        \Magento\Framework\Filesystem\DriverInterface $driver,
        \Magento\Framework\Filesystem $filesystem,
        \Aventi\SAP\Helper\Data $data,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryInterface,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemInterfaceFactory,
        \Aventi\SAP\Model\Check $check
    ) {
        parent::__construct($attribute, $logger, $driver, $filesystem);
        $this->_data = $data;
        $this->_productRepository = $productRepository;
        $this->_sourceRepository = $sourceRepository;
        $this->_filterBuilder = $filterBuilder;
        $this->_filterGroupBuilder = $filterGroupBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sourceItemRepositoryInterface = $sourceItemRepositoryInterface;
        $this->_sourceItemSave = $sourceItemSave;
        $this->_sourceItemInterfaceFactory = $sourceItemInterfaceFactory;
        $this->_check = $check;
    }

    /**
     * @return mixed
     */
    public function getLastItem()
    {
        return $this->lastItem;
    }

    /**
     * @param $item
     */
    public function setLastItem($item)
    {
        $this->lastItem = $item;
    }

    /**
     * @param $arrayItems
     */
    public function setArrayItems($arrayItems)
    {
        $this->arrayItems[] = $arrayItems;
    }

    /**
     * @return mixed
     */
    public function getArrayItems()
    {
        return $this->arrayItems;
    }

    public function unsArrayItems()
    {
        $this->arrayItems = [];
    }

    /**
     * @param $type
     * @param $fast
     * @param $source
     * @return void
     * @throws FileSystemException
     * @throws ReadingError
     */
    public function updateStock($type, $fast, $source)
    {
        $start = 0;
        $rows = 1000;
        $flag = true;
        $progressBar = null;
        while ($flag) {
            $jsonData = $this->_data->getResource($type, $start, $rows, $fast);
            $jsonPath = $this->getJsonPath($jsonData, $type);
            if ($jsonPath) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter("",Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $products = $reader->read("data");
                $progressBar = $this->startProgressBar($total);

                foreach ($products as $product) {
                    $stock = (object) [
                        'sku' => $product['ItemCode'],
                        'qty' => ($product['Quantity'] < 0) ? 0 : $product['Quantity'],
                        'source' => empty($source) ? $product['WhsCode'] : $source,
                        'isInStock' => ($product['Quantity'] <= 0) ? 0 : 1
                    ];
                    $stock->source = (empty($stock->source) ? 'default' : $stock->source);
                    $this->managerStock($stock);
                    $this->advanceProgressBar($progressBar);
                    //Debug only
                    //$total--;
                }
                $start += $rows;
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
                $this->closeFile($jsonPath);
                if ($total <= 0) {
                    $flag = false;
                }
            } else {
                $flag = false;
            }
        }
        $this->printTable($this->resTable);
    }


    /**
     * @param $stockObject
     * @return void
     */
    public function managerStock($stockObject)
    {
        try {
            if ($this->_productRepository->get($stockObject->sku)){
                if (!$sourceItem = $this->getSourceBySku($stockObject->sku, $stockObject->source)) {
                    $sourceItem = $this->_sourceItemInterfaceFactory->create();
                }
                $resultCheck = $this->_check->checkData($stockObject, $sourceItem, 'stock');
                if (!$resultCheck) {
                    $this->resTable['check']++;
                } else {
                    $sourceItem->setSourceCode($stockObject->source);
                    $sourceItem->setSku($stockObject->sku);
                    $sourceItem->setQuantity($stockObject->qty);
                    $sourceItem->setStatus($stockObject->isInStock);
                    $this->_sourceItemSave->execute([$sourceItem]);
                    $this->resTable['updated']++;
                }
            }
        } catch (\Exception $e) {
            $this->resTable['fail']++;
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $sku
     * @param $source
     * @return SourceItemInterface|null
     */
    public function getSourceBySku($sku, $source): ?\Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        $filter1 = $this->_filterBuilder
        ->setField("sku")
        ->setValue($sku)
        ->setConditionType("eq")->create();

        $filterGroup1 = $this->_filterGroupBuilder
            ->addFilter($filter1)->create();

        $filter2 = $this->_filterBuilder
            ->setField("source_code")
            ->setValue($source)
            ->setConditionType("eq")->create();

        $filterGroup2 = $this->_filterGroupBuilder
            ->addFilter($filter2)->create();

        $searchCriteria = $this->_searchCriteriaBuilder
                ->setFilterGroups([$filterGroup1, $filterGroup2])
                ->create();
        $items = $this->_sourceItemRepositoryInterface->getList($searchCriteria)->getItems();

        $source = null;
        foreach ($items as $item) {
            $source = $item;
        }
        return $source;
    }

    /**
     * @method createSourceItem
     * @brief Save the data from the $data in the instance source item $sourceItem
     * @param object $data
     * @param $sourceItem
     *
     * @author Daniel Eduardo Dorado Pérez <ddorado@aventi.co>
     * @date 16/06/2021
     */
    public function createSourceItem(object $data, $sourceItem){
        $sourceItem->setSourceCode($data->source);
        $sourceItem->setSku($data->sku);
        $sourceItem->setQuantity($data->qty);
        $sourceItem->setStatus($data->qty > 0 ? 1 : 0);
        $this->assignToSource($data->sku, $sourceItem);
        $sourceItem->save();
    }


    /**
     * @param $sku
     * @param $stockItem
     * @return void
     */
    public function assignToSource($sku, $stockItem)
    {
        try {
            if (empty($this->getLastItem())) {
                $this->setLastItem($sku);
            }

            if ($sku != $this->getLastItem()) {
                $this->_sourceItemSave->execute($this->getArrayItems());
                $this->unsArrayItems();
            }

            $this->setArrayItems($stockItem);
            $this->setLastItem($sku);
        } catch (CouldNotSaveException | InputException | ValidationException $e) {
            $this->logger->error("Error al asignar producto a la bodega: " . $e->getMessage());
        }
    }
}
