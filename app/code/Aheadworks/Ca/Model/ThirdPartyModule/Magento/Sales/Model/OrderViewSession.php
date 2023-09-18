<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Magento\Sales\Model;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

/**
 * Class OrderViewSession
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Magento\Sales\Model
 */
class OrderViewSession extends CustomerSession
{
    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $order = $this->getOrderRegistry()->getOrder();
        if ($order
            && $this->getAuthorizationManagement()->isAllowedByResource('Aheadworks_Ca::company_sales_sub_view')
        ) {
            $customerIds = $this->getCompanyUserManagement()->getChildUsersIds($customerId);
            $orderCustomerId = $order->getCustomerId();
            if (in_array($orderCustomerId, $customerIds)) {
                $customerId = $orderCustomerId;
            }
        }

        return $customerId;
    }

    /**
     * Retrieve authorization management
     *
     * @return AuthorizationManagementInterface
     */
    private function getAuthorizationManagement()
    {
        return ObjectManager::getInstance()->get(AuthorizationManagementInterface::class);
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
     * Retrieve company user management
     *
     * @return OrderRegistry
     */
    private function getOrderRegistry()
    {
        return ObjectManager::getInstance()->get(OrderRegistry::class);
    }
}
