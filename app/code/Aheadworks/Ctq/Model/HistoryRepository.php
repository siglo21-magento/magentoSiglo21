<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\HistoryRepositoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterfaceFactory;
use Aheadworks\Ctq\Api\Data\HistorySearchResultsInterface;
use Aheadworks\Ctq\Api\Data\HistorySearchResultsInterfaceFactory;
use Aheadworks\Ctq\Model\ResourceModel\History as HistoryResourceModel;
use Aheadworks\Ctq\Model\ResourceModel\History\CollectionFactory as HistoryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class HistoryRepository
 * @package Aheadworks\Ctq\Model
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var HistoryResourceModel
     */
    private $resource;

    /**
     * @var HistoryInterfaceFactory
     */
    private $historyInterfaceFactory;

    /**
     * @var HistoryCollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var HistorySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param HistoryResourceModel $resource
     * @param HistoryInterfaceFactory $historyInterfaceFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param HistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        HistoryResourceModel $resource,
        HistoryInterfaceFactory $historyInterfaceFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        HistorySearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->historyInterfaceFactory = $historyInterfaceFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(HistoryInterface $history)
    {
        try {
            $this->resource->save($history);
            $historyId = $history->getId();
            if (isset($this->registry[$historyId])) {
                $this->registry[$historyId] = null;
            }
            $history = $this->get($historyId);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $history;
    }

    /**
     * {@inheritdoc}
     */
    public function get($historyId)
    {
        if (!isset($this->registry[$historyId])) {
            /** @var HistoryInterface $history */
            $history = $this->historyInterfaceFactory->create();
            $this->resource->load($history, $historyId);
            if (!$history->getId()) {
                throw NoSuchEntityException::singleField('id', $historyId);
            }
            $this->registry[$historyId] = $history;
        }
        return $this->registry[$historyId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Ctq\Model\ResourceModel\History\Collection $collection */
        $collection = $this->historyCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, HistoryInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var HistorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var History $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param History $model
     * @return HistoryInterface
     */
    private function getDataObject($model)
    {
        /** @var HistoryInterface $object */
        $object = $this->historyInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, HistoryInterface::class),
            HistoryInterface::class
        );
        return $object;
    }
}
