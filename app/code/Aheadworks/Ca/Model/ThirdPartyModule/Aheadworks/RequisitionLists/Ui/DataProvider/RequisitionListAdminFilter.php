<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider;

use Magento\Framework\Data\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

/**
 * Class RequisitionListAdminFilter
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList
 */
class RequisitionListAdminFilter implements FilterApplierInterface
{
    /**
     * {@inheritDoc}
     */
    public function apply(Collection $collection, Filter $filter)
    {
        $whereCondition = sprintf('%s IN (%s)', $filter->getField(), $filter->getValue());
        $collection->getSelect()->where(new \Zend_Db_Expr($whereCondition));
    }
}
