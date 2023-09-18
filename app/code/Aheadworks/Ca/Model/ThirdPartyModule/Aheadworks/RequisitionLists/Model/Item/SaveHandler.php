<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\Item;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListProvider;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\Item
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var RequisitionListProvider
     */
    private $listProvider;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param ResourceConnection $resourceConnection
     * @param RequisitionListProvider $listProvider
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        RequisitionListProvider $listProvider,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->listProvider = $listProvider;
        $this->resourceConnection = $resourceConnection;
        $this->tableName = $this->resourceConnection->getTableName('aw_ca_company_requisition_lists');
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * @inheritDoc
     */
    public function execute($entity, $arguments = [])
    {
        $listId = $entity->getListId();
        if (!$listId) {
            return $entity;
        }
        $customerId = $this->getCustomerId();
        if ($customerId) {
            $this->deleteByEntity($listId);
            $this->insertLastUpdatedCustomerId($listId, $customerId);
        }

        return $entity;
    }

    /**
     * Remove customer id by list id
     *
     * @param int $listId
     * @return int
     * @throws \Exception
     */
    private function deleteByEntity($listId)
    {
        return $this->resourceConnection->getConnection()
            ->delete($this->tableName, ['company_list_id = ?' => $listId]);
    }

    /**
     * Insert customer id who updated requisition list
     *
     * @param int $listId
     * @param int $customerId
     * @return $this
     * @throws \Exception
     */
    private function insertLastUpdatedCustomerId($listId, $customerId)
    {
        $this->resourceConnection->getConnection()->insert(
            $this->tableName,
            [
                'company_list_id' => $listId,
                'updated_by' => $customerId
            ]
        );

        return $this;
    }

    /**
     * Retrieve current customer id
     *
     * @return int|null
     */
    private function getCustomerId()
    {
        if ($currentUser = $this->companyUserManagement->getCurrentUser()) {
            return $currentUser->getId();
        }

        return null;
    }
}
