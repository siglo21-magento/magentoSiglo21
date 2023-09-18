<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class RequisitionListPermission
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model
 */
class RequisitionListPermission
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var RequisitionListProvider
     */
    private $listProvider;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param RequestInterface $request
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param RequisitionListProvider $listProvider
     * @param Context $context
     */
    public function __construct(
        RequestInterface $request,
        CompanyUserManagementInterface $companyUserManagement,
        RequisitionListProvider $listProvider,
        Context $context
    ) {
        $this->request = $request;
        $this->companyUserManagement = $companyUserManagement;
        $this->listProvider = $listProvider;
        $this->context = $context;
    }

    /**
     * Is list editable by sub accounts
     *
     * @return bool
     */
    public function isEditable()
    {
        $listId = $this->request->getParam('list_id', null);
        $customerId = $this->context->getValue(OrderInterface::CUSTOMER_ID);
        if (!$customerId) {
            return false;
        }

        if (!$listId) {
            return true;
        }

        $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);

        if ($this->listProvider->getList($listId)->getCustomerId() == $customerId
            || $rootCustomer && $rootCustomer->getId() == $customerId
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check is customer has permissions to see additional information
     *
     * @param int|null $customerId
     * @return bool
     */
    public function isCustomerHasCompanyPermissions($customerId = null)
    {
        if (!$customerId) {
            return (bool)$this->companyUserManagement->getCurrentUser();
        }

        $companyIds = $this->companyUserManagement->getChildUsersIds(
            $customerId
        );

        return in_array($customerId, $companyIds);
    }

    /**
     * Check if customer has root access to requisition lists
     *
     * @param int|null $customerId
     * @return bool
     */
    public function isCustomerHasRootPermissions($customerId = null)
    {
        if (!$customerId) {
            $currentCustomer = $this->companyUserManagement->getCurrentUser();
            if (!$currentCustomer) {
                return false;
            }
            $customerId = $currentCustomer->getId();
        }

        $customer = $this->companyUserManagement->getRootUserForCustomer($customerId);
        if ($customer) {
            return $customer->getId() == $customerId;
        }

        return false;
    }
}
