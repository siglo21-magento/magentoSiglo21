<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model;

use Aheadworks\CreditLimit\Api\TransactionRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\TransactionSearchResultsInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionSearchResultsInterfaceFactory;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResource;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction\Collection as TransactionCollection;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class TransactionRepository
 *
 * @package Aheadworks\Raf\Model
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var TransactionResource
     */
    private $resource;

    /**
     * @var TransactionInterfaceFactory
     */
    private $transactionFactory;

    /**
     * @var TransactionCollectionFactory
     */
    private $transactionCollectionFactory;

    /**
     * @var TransactionSearchResultsInterfaceFactory
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
     * @param TransactionResource $resource
     * @param TransactionInterfaceFactory $transactionFactory
     * @param TransactionCollectionFactory $transactionCollectionFactory
     * @param TransactionSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        TransactionResource $resource,
        TransactionInterfaceFactory $transactionFactory,
        TransactionCollectionFactory $transactionCollectionFactory,
        TransactionSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->transactionFactory = $transactionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(TransactionInterface $transaction)
    {
        try {
            $this->resource->save($transaction);
            $this->registry[$transaction->getId()] = $transaction;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $transaction;
    }

    /**
     * @inheritdoc
     */
    public function get($transactionId)
    {
        if (!isset($this->registry[$transactionId])) {
            /** @var TransactionInterface $transaction */
            $transaction = $this->transactionFactory->create();
            $this->resource->load($transaction, $transactionId);
            if (!$transaction->getId()) {
                throw NoSuchEntityException::singleField(TransactionInterface::ID, $transactionId);
            }
            $this->registry[$transactionId] = $transaction;
        }
        return $this->registry[$transactionId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var TransactionCollection $collection */
        $collection = $this->transactionCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TransactionInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TransactionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Transaction $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Transaction $model
     * @return TransactionInterface
     */
    private function getDataObject($model)
    {
        /** @var TransactionInterface $object */
        $object = $this->transactionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, TransactionInterface::class),
            TransactionInterface::class
        );
        return $object;
    }
}
