<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteSearchResultsInterface;
use Aheadworks\Ctq\Api\Data\QuoteSearchResultsInterfaceFactory;
use Aheadworks\Ctq\Model\ResourceModel\Quote as QuoteResourceModel;
use Aheadworks\Ctq\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class QuoteRepository
 * @package Aheadworks\Ctq\Model
 */
class QuoteRepository implements QuoteRepositoryInterface
{
    /**
     * @var QuoteResourceModel
     */
    private $resource;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteInterfaceFactory;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var QuoteSearchResultsInterfaceFactory
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
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $registryByCartId = [];

    /**
     * @param QuoteResourceModel $resource
     * @param QuoteInterfaceFactory $quoteInterfaceFactory
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param QuoteSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        QuoteResourceModel $resource,
        QuoteInterfaceFactory $quoteInterfaceFactory,
        QuoteCollectionFactory $quoteCollectionFactory,
        QuoteSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->quoteInterfaceFactory = $quoteInterfaceFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QuoteInterface $quote)
    {
        try {
            $quote->setLastUpdatedAt(null);
            //@todo set orig data if empty for history
            $this->resource->save($quote);
            $this->registry[$quote->getId()] = $quote;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $quote;
    }

    /**
     * {@inheritdoc}
     */
    public function get($quoteId)
    {
        if (!isset($this->registry[$quoteId])) {
            /** @var QuoteInterface $quote */
            $quote = $this->quoteInterfaceFactory->create();
            $this->resource->load($quote, $quoteId);
            if (!$quote->getId()) {
                throw NoSuchEntityException::singleField('id', $quoteId);
            }
            $this->registry[$quoteId] = $quote;
            $this->registryByCartId[$quote->getCartId()] = $quote;
        }
        return $this->registry[$quoteId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCartId($cartId)
    {
        if (!isset($this->registryByCartId[$cartId])) {
            $quoteId = $this->resource->getIdByCartId($cartId);
            if (!$quoteId) {
                throw NoSuchEntityException::singleField('cart id', $cartId);
            }
            /** @var QuoteInterface $quote */
            $quote = $this->quoteInterfaceFactory->create();
            $this->resource->load($quote, $quoteId);
            $this->registry[$quoteId] = $quote;
            $this->registryByCartId[$cartId] = $quote;
        }
        return $this->registryByCartId[$cartId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Ctq\Model\ResourceModel\Quote\Collection $collection */
        $collection = $this->quoteCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, QuoteInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var QuoteSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Quote $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QuoteInterface $quote)
    {
        try {
            $this->resource->delete($quote);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$quote->getId()])) {
            unset($this->registry[$quote->getId()]);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($quoteId)
    {
        return $this->delete($this->get($quoteId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Quote $model
     * @return QuoteInterface
     */
    private function getDataObject($model)
    {
        /** @var QuoteInterface $object */
        $object = $this->quoteInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            QuoteInterface::class
        );
        $object->setOrigData();
        return $object;
    }
}
