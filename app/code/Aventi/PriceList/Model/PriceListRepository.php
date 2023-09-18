<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceList\Model;

use Aventi\PriceList\Api\Data\PriceListInterfaceFactory;
use Aventi\PriceList\Api\Data\PriceListSearchResultsInterfaceFactory;
use Aventi\PriceList\Api\PriceListRepositoryInterface;
use Aventi\PriceList\Model\ResourceModel\PriceList as ResourcePriceList;
use Aventi\PriceList\Model\ResourceModel\PriceList\CollectionFactory as PriceListCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class PriceListRepository implements PriceListRepositoryInterface
{

    private $collectionProcessor;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    private $storeManager;

    protected $dataPriceListFactory;

    protected $priceListFactory;

    protected $priceListCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    protected $dataObjectHelper;


    /**
     * @param ResourcePriceList $resource
     * @param PriceListFactory $priceListFactory
     * @param PriceListInterfaceFactory $dataPriceListFactory
     * @param PriceListCollectionFactory $priceListCollectionFactory
     * @param PriceListSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePriceList $resource,
        PriceListFactory $priceListFactory,
        PriceListInterfaceFactory $dataPriceListFactory,
        PriceListCollectionFactory $priceListCollectionFactory,
        PriceListSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->priceListFactory = $priceListFactory;
        $this->priceListCollectionFactory = $priceListCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPriceListFactory = $dataPriceListFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Aventi\PriceList\Api\Data\PriceListInterface $priceList
    ) {
        /* if (empty($priceList->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $priceList->setStoreId($storeId);
        } */
        
        $priceListData = $this->extensibleDataObjectConverter->toNestedArray(
            $priceList,
            [],
            \Aventi\PriceList\Api\Data\PriceListInterface::class
        );
        
        $priceListModel = $this->priceListFactory->create()->setData($priceListData);
        
        try {
            $this->resource->save($priceListModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the priceList: %1',
                $exception->getMessage()
            ));
        }
        return $priceListModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($priceListId)
    {
        $priceList = $this->priceListFactory->create();
        $this->resource->load($priceList, $priceListId);
        if (!$priceList->getId()) {
            throw new NoSuchEntityException(__('PriceList with id "%1" does not exist.', $priceListId));
        }
        return $priceList->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->priceListCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\PriceList\Api\Data\PriceListInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Aventi\PriceList\Api\Data\PriceListInterface $priceList
    ) {
        try {
            $priceListModel = $this->priceListFactory->create();
            $this->resource->load($priceListModel, $priceList->getPricelistId());
            $this->resource->delete($priceListModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the PriceList: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($priceListId)
    {
        return $this->delete($this->get($priceListId));
    }
}

