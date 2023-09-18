<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ListCustomerSession
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Model
 */
class ListCustomerSession extends CustomerSession
{
    /**
     * @var array
     */
    private $customerIdsCache = [];

    /**
     * @var array
     */
    private $listCache = [];

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $listId = $this->getListId();

        if ($listId) {
            if (!isset($this->customerIdsCache[$customerId])) {
                $this->customerIdsCache[$customerId] = $this->getCompanyUserManagement()
                    ->getChildUsersIds($customerId);
            }

            $customerIds = $this->customerIdsCache[$customerId];
            $list = $this->getList($listId);

            if (!$list) {
                return $customerId;
            }

            $listCustomerId = $list->getCustomerId();

            if (in_array($listCustomerId, $customerIds)) {
                if ($list->getShared()) {
                    $customerId = $listCustomerId;
                } elseif ($this->getListPermissions()->isCustomerHasRootPermissions($customerId)) {
                    $customerId = $listCustomerId;
                }
            }
        }

        return $customerId;
    }

    /**
     * Retrieve company user management
     *
     * @return CompanyUserManagementInterface
     */
    private function getCompanyUserManagement()
    {
        return ObjectManager::getInstance()->get(CompanyUserManagementInterface::class);
    }

    /**
     * Retrieve quote id
     *
     * @return int
     */
    private function getListId()
    {
        $request = ObjectManager::getInstance()->get(RequestInterface::class);
        return $request->getParam('list_id');
    }

    /**
     * Get Requisition List Permissions
     *
     * @return RequisitionListPermission
     */
    private function getListPermissions()
    {
        return ObjectManager::getInstance()->get(RequisitionListPermission::class);
    }

    /**
     * Retrieve list
     *
     * @param int $listId
     * @return RequisitionListItemInterface|null
     */
    private function getList($listId)
    {
        if (!isset($this->listCache[$listId])) {
            $listRepository = ObjectManager::getInstance()->get(RequisitionListRepositoryInterface::class);
            try {
                $this->listCache[$listId] = $listRepository->get($listId);
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }

        return $this->listCache[$listId];
    }
}
