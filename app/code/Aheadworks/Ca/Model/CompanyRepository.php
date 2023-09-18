<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyInterfaceFactory;
use Aheadworks\Ca\Api\Data\CompanySearchResultsInterface;
use Aheadworks\Ca\Api\Data\CompanySearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\ResourceModel\Company as CompanyResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Company\CollectionFactory as CompanyCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class CompanyRepository
 * @package Aheadworks\Ca\Model
 */
class CompanyRepository implements CompanyRepositoryInterface
{
    /**
     * @var CompanyResourceModel
     */
    private $resource;

    /**
     * @var CompanyInterfaceFactory
     */
    private $companyInterfaceFactory;

    /**
     * @var CompanyCollectionFactory
     */
    private $companyCollectionFactory;

    /**
     * @var CompanySearchResultsInterfaceFactory
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
     * @param CompanyResourceModel $resource
     * @param CompanyInterfaceFactory $companyInterfaceFactory
     * @param CompanyCollectionFactory $companyCollectionFactory
     * @param CompanySearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        CompanyResourceModel $resource,
        CompanyInterfaceFactory $companyInterfaceFactory,
        CompanyCollectionFactory $companyCollectionFactory,
        CompanySearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->companyInterfaceFactory = $companyInterfaceFactory;
        $this->companyCollectionFactory = $companyCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CompanyInterface $company)
    {
        try {
            $this->resource->save($company);
            $companyId = $company->getId();
            $this->registry[$companyId] = $company;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $company;
    }

    /**
     * {@inheritdoc}
     */
    public function get($companyId)
    {
        if (!isset($this->registry[$companyId])) {
            /** @var CompanyInterface $company */
            $company = $this->companyInterfaceFactory->create();
            $this->resource->load($company, $companyId);
            if (!$company->getId()) {
                throw NoSuchEntityException::singleField('id', $companyId);
            }
            $this->registry[$companyId] = $company;
        }
        return $this->registry[$companyId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Ca\Model\ResourceModel\Company\Collection $collection */
        $collection = $this->companyCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CompanyInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CompanySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Company $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Company $model
     * @return CompanyInterface
     */
    private function getDataObject($model)
    {
        /** @var CompanyInterface $object */
        $object = $this->companyInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, CompanyInterface::class),
            CompanyInterface::class
        );
        return $object;
    }
}
