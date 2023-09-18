<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model;

use Aventi\SAP\Api\Data\DocumentStatusInterfaceFactory;
use Aventi\SAP\Api\Data\DocumentStatusSearchResultsInterfaceFactory;
use Aventi\SAP\Api\DocumentStatusRepositoryInterface;
use Aventi\SAP\Model\ResourceModel\DocumentStatus as ResourceDocumentStatus;
use Aventi\SAP\Model\ResourceModel\DocumentStatus\CollectionFactory as DocumentStatusCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class DocumentStatusRepository implements DocumentStatusRepositoryInterface
{
    protected $searchResultsFactory;

    private $storeManager;

    protected $documentStatusFactory;

    protected $documentStatusCollectionFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;

    protected $dataDocumentStatusFactory;

    protected $dataObjectHelper;

    protected $extensibleDataObjectConverter;
    protected $resource;

    /**
     * @param ResourceDocumentStatus $resource
     * @param DocumentStatusFactory $documentStatusFactory
     * @param DocumentStatusInterfaceFactory $dataDocumentStatusFactory
     * @param DocumentStatusCollectionFactory $documentStatusCollectionFactory
     * @param DocumentStatusSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceDocumentStatus $resource,
        DocumentStatusFactory $documentStatusFactory,
        DocumentStatusInterfaceFactory $dataDocumentStatusFactory,
        DocumentStatusCollectionFactory $documentStatusCollectionFactory,
        DocumentStatusSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->documentStatusFactory = $documentStatusFactory;
        $this->documentStatusCollectionFactory = $documentStatusCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataDocumentStatusFactory = $dataDocumentStatusFactory;
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
        \Aventi\SAP\Api\Data\DocumentStatusInterface $documentStatus
    ) {
        /* if (empty($documentStatus->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $documentStatus->setStoreId($storeId);
        } */

        $documentStatusData = $this->extensibleDataObjectConverter->toNestedArray(
            $documentStatus,
            [],
            \Aventi\SAP\Api\Data\DocumentStatusInterface::class
        );

        $documentStatusModel = $this->documentStatusFactory->create()->setData($documentStatusData);

        try {
            $this->resource->save($documentStatusModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the documentStatus: %1',
                $exception->getMessage()
            ));
        }
        return $documentStatusModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($documentStatusId)
    {
        $documentStatus = $this->documentStatusFactory->create();
        $this->resource->load($documentStatus, $documentStatusId);
        if (!$documentStatus->getId()) {
            throw new NoSuchEntityException(__('DocumentStatus with id "%1" does not exist.', $documentStatusId));
        }
        return $documentStatus->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getByParentId($documentQuoteId)
    {
        $documentStatus = $this->documentStatusFactory->create();
        $this->resource->load($documentStatus, $documentQuoteId, 'parent_id');
        if (!$documentStatus->getId()) {
            throw new NoSuchEntityException(__('DocumentStatus with parent "%1" does not exist.', $documentQuoteId));
        }
        return $documentStatus->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->documentStatusCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\SAP\Api\Data\DocumentStatusInterface::class
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
        \Aventi\SAP\Api\Data\DocumentStatusInterface $documentStatus
    ) {
        try {
            $documentStatusModel = $this->documentStatusFactory->create();
            $this->resource->load($documentStatusModel, $documentStatus->getDocumentstatusId());
            $this->resource->delete($documentStatusModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the DocumentStatus: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($documentStatusId)
    {
        return $this->delete($this->get($documentStatusId));
    }
}
