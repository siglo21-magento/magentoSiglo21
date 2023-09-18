<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel\Company\Relation\PaymentMethods;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Ca\Model\ResourceModel\Company\Relation\PaymentMethods
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
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tableName = $this->resourceConnection->getTableName(Company::COMPANY_PAYMENTS_TABLE_NAME);
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var CompanyInterface $entity */
        $entityId = (int)$entity->getId();
        if (!$entityId) {
            return $entity;
        }

        $this->deleteByEntity($entityId);
        $toInsert = $this->getAllowedPaymentMethods($entity);
        $this->insertAllowedPaymentMethods($toInsert);

        return $entity;
    }

    /**
     * Remove payment codes by company id
     *
     * @param int $companyId
     * @return int
     * @throws \Exception
     */
    private function deleteByEntity($companyId)
    {
        return $this->getConnection()->delete($this->tableName, ['company_id = ?' => $companyId]);
    }

    /**
     * Retrieve array of payment data to insert
     *
     * @param CompanyInterface $entity
     * @return array
     */
    private function getAllowedPaymentMethods($entity)
    {
        $allowedPaymentMethods = [];
        $paymentCodes = $entity->getAllowedPaymentMethods() ? : [];
        foreach ($paymentCodes as $paymentCode) {
            $allowedPaymentMethods[] = [
                'company_id' => (int)$entity->getId(),
                'payment_name' => $paymentCode
            ];
        }
        return $allowedPaymentMethods;
    }

    /**
     * Insert allowed payment methods
     *
     * @param array $toInsert
     * @return $this
     * @throws \Exception
     */
    private function insertAllowedPaymentMethods($toInsert)
    {
        if (!empty($toInsert)) {
            $this->getConnection()->insertMultiple($this->tableName, $toInsert);
        }
        return $this;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(CompanyInterface::class)->getEntityConnectionName()
        );
    }
}
