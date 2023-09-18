<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

/**
 * Class RequisitionListFilter
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList
 */
class RequisitionListFilter implements FilterApplierInterface
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Collection $collection, Filter $filter)
    {
        $whereCondition = sprintf(
            '%s IN (%s) OR (shared = 1 AND %s IN (%s))',
            $filter->getField(),
            $filter->getValue(),
            'customer_id',
            implode(', ', $this->companyUserManagement->getChildUsersIds($filter->getValue()))
        );
        $collection->getSelect()->where(new \Zend_Db_Expr($whereCondition));
    }
}
