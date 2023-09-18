<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Company;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class WebsiteFilter
 * @package Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Company
 */
class WebsiteFilter implements CustomFilterInterface
{
    /**
     * Apply website filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $fieldToFilter = 'website_id';
        $collection->addWebsiteFilter();
        $collection->addFilterToMap($fieldToFilter, 'customer.' . $fieldToFilter);
        $collection->addFilter($fieldToFilter, $filter->getValue(), 'public');

        return true;
    }
}
