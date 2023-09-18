<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\CommentRepositoryInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Api\Data\CommentSearchResultsInterface;
use Aheadworks\Ctq\Api\Data\CommentSearchResultsInterfaceFactory;
use Aheadworks\Ctq\Model\ResourceModel\Comment as CommentResourceModel;
use Aheadworks\Ctq\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class CommentRepository
 * @package Aheadworks\Ctq\Model
 */
class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @var CommentResourceModel
     */
    private $resource;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentInterfaceFactory;

    /**
     * @var CommentCollectionFactory
     */
    private $commentCollectionFactory;

    /**
     * @var CommentSearchResultsInterfaceFactory
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
     * @param CommentResourceModel $resource
     * @param CommentInterfaceFactory $commentInterfaceFactory
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param CommentSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        CommentResourceModel $resource,
        CommentInterfaceFactory $commentInterfaceFactory,
        CommentCollectionFactory $commentCollectionFactory,
        CommentSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->commentInterfaceFactory = $commentInterfaceFactory;
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CommentInterface $comment)
    {
        try {
            //@todo set orig data if empty for history
            $this->resource->save($comment);
            $commentId = $comment->getId();
            if (isset($this->registry[$commentId])) {
                $this->registry[$commentId] = null;
            }
            $comment = $this->get($commentId);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function get($commentId)
    {
        if (!isset($this->registry[$commentId])) {
            /** @var CommentInterface $comment */
            $comment = $this->commentInterfaceFactory->create();
            $this->resource->load($comment, $commentId);
            if (!$comment->getId()) {
                throw NoSuchEntityException::singleField('id', $commentId);
            }
            $this->registry[$commentId] = $comment;
        }
        return $this->registry[$commentId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Ctq\Model\ResourceModel\Comment\Collection $collection */
        $collection = $this->commentCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CommentInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CommentSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Comment $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Comment $model
     * @return CommentInterface
     */
    private function getDataObject($model)
    {
        /** @var CommentInterface $object */
        $object = $this->commentInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, CommentInterface::class),
            CommentInterface::class
        );
        return $object;
    }
}
