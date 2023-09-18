<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel\Transaction\Relation\Entity;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResource;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel\Transaction\Relation\Entity
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TransactionEntityInterfaceFactory
     */
    private $entityFactory;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param TransactionEntityInterfaceFactory $entityFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        TransactionEntityInterfaceFactory $entityFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->entityFactory = $entityFactory;
        $this->tableName = $this->resourceConnection->getTableName(TransactionResource::TRANSACTION_ENTITY_TABLE);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if (!(int)$entity->getId()) {
            return $entity;
        }

        $entities = $this->getEntityObjects($entity->getId());
        $entity->setEntities($entities);

        return $entity;
    }

    /**
     * Retrieve entity objects
     *
     * @param int $transactionId
     * @return TransactionEntityInterface[]
     * @throws \Exception
     */
    private function getEntityObjects($transactionId)
    {
        $objects = [];
        $entities = $this->getEntities($transactionId);
        foreach ($entities as $entity) {
            /** @var TransactionEntityInterface $entityFactory */
            $entityObject = $this->entityFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $entityObject,
                $entity,
                TransactionEntityInterface::class
            );
            $objects[] = $entityObject;
        }

        return $objects;
    }

    /**
     * Retrieve entities
     *
     * @param int $transactionId
     * @return array
     * @throws \Exception
     */
    private function getEntities($transactionId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName)
            ->where(TransactionEntityInterface::TRANSACTION_ID . ' = :transaction_id');
        return $connection->fetchAssoc($select, [TransactionEntityInterface::TRANSACTION_ID => $transactionId]);
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TransactionInterface::class)->getEntityConnectionName()
        );
    }
}
