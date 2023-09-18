<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Api\Data\GroupSearchResultsInterface;
use Aheadworks\Ca\Api\GroupRepositoryInterface;
use Aheadworks\Ca\Model\ResourceModel\Group as GroupResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Aheadworks\Ca\Api\Data\GroupInterfaceFactory;
use Aheadworks\Ca\Api\Data\GroupSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class GroupRepository
 * @package Aheadworks\Ca\Model
 */
class GroupRepository implements GroupRepositoryInterface
{
    /**
     * @var GroupResourceModel
     */
    private $resource;

    /**
     * @var GroupInterfaceFactory
     */
    private $groupInterfaceFactory;

    /**
     * @var GroupCollectionFactory
     */
    private $groupCollectionFactory;

    /**
     * @var GroupSearchResultsInterfaceFactory
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
     * @param GroupResourceModel $resource
     * @param GroupInterfaceFactory $groupInterfaceFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param GroupSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        GroupResourceModel $resource,
        GroupInterfaceFactory $groupInterfaceFactory,
        GroupCollectionFactory $groupCollectionFactory,
        GroupSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->groupInterfaceFactory = $groupInterfaceFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(GroupInterface $group)
    {
        try {
            $this->resource->save($group);
            $groupId = $group->getId();
            $this->registry[$groupId] = $group;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function get($groupId)
    {
        if (!isset($this->registry[$groupId])) {
            /** @var GroupInterface $group */
            $group = $this->groupInterfaceFactory->create();
            $this->resource->load($group, $groupId);
            if (!$group->getId()) {
                throw NoSuchEntityException::singleField('id', $groupId);
            }
            $this->registry[$groupId] = $group;
        }
        return $this->registry[$groupId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Ca\Model\ResourceModel\Group\Collection $collection */
        $collection = $this->groupCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, GroupInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var GroupSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Group $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Group $model
     * @return GroupInterface
     */
    private function getDataObject($model)
    {
        /** @var GroupInterface $object */
        $object = $this->groupInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, GroupInterface::class),
            GroupInterface::class
        );
        return $object;
    }
}
