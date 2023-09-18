<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel\Transaction\Relation\Entity;

use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResource;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel\Transaction\Relation\Entity
 */
class SaveHandler implements ExtensionInterface
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
     * @var string
     */
    private $tableName;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tableName = $this->resourceConnection->getTableName(TransactionResource::TRANSACTION_ENTITY_TABLE);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if (empty($entity->getEntities())) {
            return $entity;
        }

        $this->deleteByEntity($entity->getId());
        $entities = [];
        /** @var TransactionEntityInterface $transactionEntity */
        foreach ($entity->getEntities() as $transactionEntity) {
            $entities[] = [
                TransactionEntityInterface::TRANSACTION_ID => $entity->getId(),
                TransactionEntityInterface::ENTITY_ID => $transactionEntity->getEntityId(),
                TransactionEntityInterface::ENTITY_LABEL => $transactionEntity->getEntityLabel(),
                TransactionEntityInterface::ENTITY_TYPE => $transactionEntity->getEntityType(),
            ];
        }
        $this->getConnection()->insertMultiple($this->tableName, $entities);

        return $entity;
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

    /**
     * Remove transaction entity by transaction ID
     *
     * @param int $transactionId
     * @return int
     * @throws \Exception
     */
    private function deleteByEntity($transactionId)
    {
        return $this->getConnection()->delete(
            $this->tableName,
            [TransactionEntityInterface::TRANSACTION_ID . ' = ?' => $transactionId]
        );
    }
}
