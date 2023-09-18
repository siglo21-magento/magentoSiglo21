<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment;

use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Pool;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class EntityConverter
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment
 */
class EntityConverter
{
    /**
     * @var TransactionEntityInterfaceFactory
     */
    private $transactionEntityFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Pool
     */
    private $converterPool;

    /**
     * @param TransactionEntityInterfaceFactory $transactionEntityFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param Pool $converterPool
     */
    public function __construct(
        TransactionEntityInterfaceFactory $transactionEntityFactory,
        DataObjectHelper $dataObjectHelper,
        Pool $converterPool
    ) {
        $this->transactionEntityFactory = $transactionEntityFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->converterPool = $converterPool;
    }

    /**
     * Convert provided entities to transaction entities
     *
     * @param DataObject[]|DataObject $entities
     * @return TransactionEntityInterface[]
     */
    public function convert($entities)
    {
        $transactionEntities = [];
        $entities = is_array($entities) ? $entities : [$entities];
        foreach ($entities as $entity) {
            $converter = $this->converterPool->getConverter($entity->getEventObject());
            if ($converter) {
                $transactionEntities[] = $converter->convertToTransactionEntity($entity);
            }
        }

        return $transactionEntities;
    }

    /**
     * Convert array with row entities to object entities
     *
     * @param array $arrayWithEntities
     * @return TransactionEntityInterface[]
     */
    public function convertFromArrayToObject($arrayWithEntities)
    {
        $entityObjects = [];
        foreach ($arrayWithEntities as $entityData) {
            /** @var TransactionEntityInterface $transactionEntity */
            $entityObject = $this->transactionEntityFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $entityObject,
                $entityData,
                TransactionEntityInterface::class
            );
            $entityObjects[] = $entityObject;
        }

        return $entityObjects;
    }
}
