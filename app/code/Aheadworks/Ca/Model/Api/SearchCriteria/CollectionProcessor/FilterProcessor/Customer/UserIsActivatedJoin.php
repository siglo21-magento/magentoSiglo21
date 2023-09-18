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
 * Class UserIsActivatedJoin
 *
 * @package Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Customer
 */
class UserIsActivatedJoin implements CustomFilterInterface
{
    /**
     * Join is activated field to use sorting by this field.
     *
     * We use filter processor for joining since join processor is not available for customer collection.
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $collection->joinField(
            'aw_ca_company_user_is_activated',
            'aw_ca_company_user',
            'is_activated',
            'customer_id=entity_id'
        );

        return true;
    }
}
