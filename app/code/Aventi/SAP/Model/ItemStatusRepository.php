<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model;

use Aventi\SAP\Api\Data\ItemStatusInterfaceFactory;
use Aventi\SAP\Api\Data\ItemStatusSearchResultsInterfaceFactory;
use Aventi\SAP\Api\ItemStatusRepositoryInterface;
use Aventi\SAP\Model\ResourceModel\ItemStatus as ResourceItemStatus;
use Aventi\SAP\Model\ResourceModel\ItemStatus\CollectionFactory as ItemStatusCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class ItemStatusRepository implements ItemStatusRepositoryInterface
{

    protected $dataItemStatusFactory;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    private $storeManager;

    protected $itemStatusCollectionFactory;

    protected $dataObjectHelper;

    protected $itemStatusFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceItemStatus $resource
     * @param ItemStatusFactory $itemStatusFactory
     * @param ItemStatusInterfaceFactory $dataItemStatusFactory
     * @param ItemStatusCollectionFactory $itemStatusCollectionFactory
     * @param ItemStatusSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceItemStatus $resource,
        ItemStatusFactory $itemStatusFactory,
        ItemStatusInterfaceFactory $dataItemStatusFactory,
        ItemStatusCollectionFactory $itemStatusCollectionFactory,
        ItemStatusSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->itemStatusFactory = $itemStatusFactory;
        $this->itemStatusCollectionFactory = $itemStatusCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataItemStatusFactory = $dataItemStatusFactory;
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
        \Aventi\SAP\Api\Data\ItemStatusInterface $itemStatus
    ) {
        /* if (empty($itemStatus->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $itemStatus->setStoreId($storeId);
        } */

        $itemStatusData = $this->extensibleDataObjectConverter->toNestedArray(
            $itemStatus,
            [],
            \Aventi\SAP\Api\Data\ItemStatusInterface::class
        );

        $itemStatusModel = $this->itemStatusFactory->create()->setData($itemStatusData);

        try {
            $this->resource->save($itemStatusModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the itemStatus: %1',
                $exception->getMessage()
            ));
        }
        return $itemStatusModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($itemStatusId)
    {
        $itemStatus = $this->itemStatusFactory->create();
        $this->resource->load($itemStatus, $itemStatusId);
        if (!$itemStatus->getId()) {
            throw new NoSuchEntityException(__('ItemStatus with id "%1" does not exist.', $itemStatusId));
        }
        return $itemStatus->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getByItemId($itemId)
    {
        $itemStatus = $this->itemStatusFactory->create();
        $this->resource->load($itemStatus, $itemId, 'item_id');
        if (!$itemStatus->getId()) {
            throw new NoSuchEntityException(__('ItemStatus with item id "%1" does not exist.', $itemId));
        }
        return $itemStatus->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->itemStatusCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\SAP\Api\Data\ItemStatusInterface::class
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
        \Aventi\SAP\Api\Data\ItemStatusInterface $itemStatus
    ) {
        try {
            $itemStatusModel = $this->itemStatusFactory->create();
            $this->resource->load($itemStatusModel, $itemStatus->getItemstatusId());
            $this->resource->delete($itemStatusModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ItemStatus: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($itemStatusId)
    {
        return $this->delete($this->get($itemStatusId));
    }
}
