<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Comment;

use Aheadworks\Ctq\Model\ResourceModel\Comment\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class AttachmentFileNameFilter
 * @package Aheadworks\Ctq\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Comment
 */
class AttachmentFileNameFilter implements CustomFilterInterface
{
    /**
     * Apply custom attachment filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        /** @var Collection $collection */
        $collection->addAttachmentFileNameFilter($filter->getValue());

        return true;
    }
}
