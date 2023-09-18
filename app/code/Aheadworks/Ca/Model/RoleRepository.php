<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\Data\RoleSearchResultsInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Model\ResourceModel\Role as RoleResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Role\CollectionFactory as RoleCollectionFactory;
use Aheadworks\Ca\Api\Data\RoleInterfaceFactory;
use Aheadworks\Ca\Api\Data\RoleSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RoleRepository
 * @package Aheadworks\Ca\Model
 */
class RoleRepository implements RoleRepositoryInterface
{
    /**
     * @var RoleResourceModel
     */
    private $resource;

    /**
     * @var RoleInterfaceFactory
     */
    private $roleInterfaceFactory;

    /**
     * @var RoleCollectionFactory
     */
    private $roleCollectionFactory;

    /**
     * @var RoleSearchResultsInterfaceFactory
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
     * @param RoleResourceModel $resource
     * @param RoleInterfaceFactory $roleInterfaceFactory
     * @param RoleCollectionFactory $roleCollectionFactory
     * @param RoleSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        RoleResourceModel $resource,
        RoleInterfaceFactory $roleInterfaceFactory,
        RoleCollectionFactory $roleCollectionFactory,
        RoleSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->roleInterfaceFactory = $roleInterfaceFactory;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(RoleInterface $role)
    {
        try {
            $this->resource->save($role);
            $roleId = $role->getId();
            $this->registry[$roleId] = $role;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function get($roleId)
    {
        if (!isset($this->registry[$roleId])) {
            /** @var RoleInterface $role */
            $role = $this->roleInterfaceFactory->create();
            $this->resource->load($role, $roleId);
            if (!$role->getId()) {
                throw NoSuchEntityException::singleField('id', $roleId);
            }
            $this->registry[$roleId] = $role;
        }
        return $this->registry[$roleId];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultUserRole($companyId)
    {
        $roleId = $this->resource->getDefaultUserRole($companyId);
        if (!$roleId) {
            throw NoSuchEntityException::singleField('id', $roleId);
        }

        return $this->get($roleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Ca\Model\ResourceModel\Role\Collection $collection */
        $collection = $this->roleCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, RoleInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RoleSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Role $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Role $model
     * @return RoleInterface
     */
    private function getDataObject($model)
    {
        /** @var RoleInterface $object */
        $object = $this->roleInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, RoleInterface::class),
            RoleInterface::class
        );
        return $object;
    }
}
