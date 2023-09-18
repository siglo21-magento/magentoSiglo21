<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class ListingDataProvider
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList
 */
class ListingDataProvider extends DataProvider
{
    /**#@+
     * Condition Types
     */
    const EQ_CONDITION = 'eq';
    const REQUISITION_LIST_MERGE_WITH_SUB_CONDITION = 'rlist';
    const REQUISITION_LIST_MERGE_WITH_SUB_CONDITION_ADMIN = 'rlist_admin';
    /**#@-/

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;
    
    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CustomerSession $customerSession
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param RequisitionListPermission $listPermission
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CustomerSession $customerSession,
        CompanyUserManagementInterface $companyUserManagement,
        RequisitionListPermission $listPermission,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->customerSession = $customerSession;
        $this->companyUserManagement = $companyUserManagement;
        $this->listPermission = $listPermission;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        $customerIds = [];
        if ($customerId = $this->customerSession->getCustomerId()) {
            $condition = self::EQ_CONDITION;
            if ($this->listPermission->isCustomerHasCompanyPermissions($customerId)) {
                $condition = self::REQUISITION_LIST_MERGE_WITH_SUB_CONDITION;
                if ($this->listPermission->isCustomerHasRootPermissions($customerId)) {
                    $condition = self::REQUISITION_LIST_MERGE_WITH_SUB_CONDITION_ADMIN;
                    $companyCustomersIds = $this->companyUserManagement->getChildUsersIds($customerId);
                    $customerIds = $companyCustomersIds;
                }
            }

            $customerIds = array_merge(
                $customerIds,
                array_diff([$customerId], $customerIds)
            );

            $filter = $this->filterBuilder
                ->setField(OrderInterface::CUSTOMER_ID)
                ->setValue(implode(', ', $customerIds))
                ->setConditionType($condition)->create();
            $this->addFilter($filter);
        }

        return parent::getSearchCriteria();
    }
}
