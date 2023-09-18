<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Quote;

use Aheadworks\Ctq\Model\ResourceModel\Quote\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class ReminderFilter
 *
 * @package Aheadworks\Ctq\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Quote
 */
class WithNotGivenReminderDateFilter implements CustomFilterInterface
{
    /**
     * Apply custom expire filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        /** @var Collection $collection */
        $collection->addNotGivenQuoteReminderDateFilter();

        return true;
    }
}
