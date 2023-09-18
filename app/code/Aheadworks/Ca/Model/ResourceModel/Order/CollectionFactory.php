<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel\Order;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Class CollectionFactory
 * @package Aheadworks\Ca\Model\ResourceModel\Order
 */
class CollectionFactory implements CollectionFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        CompanyUserManagementInterface $companyUserManagement,
        AuthorizationManagementInterface $authorizationManagement,
        $instanceName = Collection::class
    ) {
        $this->objectManager = $objectManager;
        $this->companyUserManagement = $companyUserManagement;
        $this->authorizationManagement = $authorizationManagement;
        $this->instanceName = $instanceName;
    }

    /**
     * {@inheritdoc}
     */
    public function create($customerId = null)
    {
        /** @var Collection $collection */
        $collection = $this->objectManager->create($this->instanceName);

        if ($customerId) {
            $customers = [];
            if ($this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_sales_sub_view')) {
                $customers = $this->companyUserManagement->getChildUsersIds($customerId);
            }

            $customers[] = $customerId;
            $collection->addFieldToFilter('customer_id', ['in' => $customers]);
        }

        return $collection;
    }
}
