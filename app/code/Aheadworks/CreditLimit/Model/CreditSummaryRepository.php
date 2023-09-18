<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model;

use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\SummarySearchResultsInterface;
use Aheadworks\CreditLimit\Api\Data\SummarySearchResultsInterfaceFactory;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary as CreditSummaryResourceModel;
use Aheadworks\CreditLimit\Model\ResourceModel\Customer\Collection as CreditSummaryCollection;
use Aheadworks\CreditLimit\Model\ResourceModel\Customer\CollectionFactory as CreditSummaryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class CreditSummaryRepository
 *
 * @package Aheadworks\CreditLimit\Model
 */
class CreditSummaryRepository implements SummaryRepositoryInterface
{
    /**
     * @var CreditSummaryResourceModel
     */
    private $resource;

    /**
     * @var SummaryInterfaceFactory
     */
    private $creditSummaryFactory;

    /**
     * @var CreditSummaryCollectionFactory
     */
    private $creditSummaryCollectionFactory;

    /**
     * @var SummarySearchResultsInterfaceFactory
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
     * @var array
     */
    private $registryByCustomerId = [];

    /**
     * @param CreditSummaryResourceModel $resource
     * @param SummaryInterfaceFactory $creditSummaryFactory
     * @param CreditSummaryCollectionFactory $creditSummaryCollectionFactory
     * @param SummarySearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        CreditSummaryResourceModel $resource,
        SummaryInterfaceFactory $creditSummaryFactory,
        CreditSummaryCollectionFactory $creditSummaryCollectionFactory,
        SummarySearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->creditSummaryFactory = $creditSummaryFactory;
        $this->creditSummaryCollectionFactory = $creditSummaryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(SummaryInterface $creditSummary)
    {
        try {
            $this->resource->save($creditSummary);
            $this->registry[$creditSummary->getSummaryId()] = $creditSummary;
            $this->registryByCustomerId[$creditSummary->getCustomerId()] = $creditSummary;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $creditSummary;
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerId($customerId, $reload = false)
    {
        if (!isset($this->registryByCustomerId[$customerId]) || $reload) {
            $creditSummaryData = $this->resource->loadByCustomerId($customerId);
            if (!$creditSummaryData) {
                throw NoSuchEntityException::singleField(SummaryInterface::CUSTOMER_ID, $customerId);
            }
            $creditSummary = $this->prepareDataObjectFromRowData($creditSummaryData);
            if ($creditSummary->getSummaryId()) {
                $this->registry[$creditSummary->getSummaryId()] = $creditSummary;
            }
            $this->registryByCustomerId[$customerId] = $creditSummary;
        }
        return $this->registryByCustomerId[$customerId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CreditSummaryCollection $collection */
        $collection = $this->creditSummaryCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, SummaryInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SummarySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var SummaryInterface $creditSummary */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->prepareDataObjectFromModel($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object from model
     *
     * @param CreditSummary|DataObject|array $model
     * @return SummaryInterface
     */
    private function prepareDataObjectFromModel($model)
    {
        /** @var SummaryInterface $object */
        $object = $this->creditSummaryFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            is_array($model) ? $model : $model->getData(),
            SummaryInterface::class
        );

        return $object;
    }

    /**
     * Prepare data object from row data array
     *
     * @param array $dataArray
     * @return SummaryInterface
     */
    private function prepareDataObjectFromRowData($dataArray)
    {
        $notFormattedDataObject = $this->prepareDataObjectFromModel($dataArray);
        $formattedData = $this->dataObjectProcessor->buildOutputDataArray(
            $notFormattedDataObject,
            SummaryInterface::class
        );

        return $this->prepareDataObjectFromModel($formattedData);
    }
}
