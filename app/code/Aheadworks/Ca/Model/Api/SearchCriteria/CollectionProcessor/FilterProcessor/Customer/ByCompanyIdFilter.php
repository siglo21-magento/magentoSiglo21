<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Customer;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Model\ResourceModel\Group;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class ByCompanyIdFilter
 * @package Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Customer
 */
class ByCompanyIdFilter implements CustomFilterInterface
{
    /**
     * Apply company id filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $fieldToFilter = CompanyUserInterface::COMPANY_ID;
        $collection->addFilterToMap($fieldToFilter, 'extension_attribute_aw_ca_company_user.' . $fieldToFilter);
        $collection->addFilter($fieldToFilter, $filter->getValue(), 'public');

        return true;
    }
}
