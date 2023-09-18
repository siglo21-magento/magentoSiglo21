<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Model\ResourceModel\Grid\CompanyColumn;

use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\Ca\Model\ResourceModel\Company\Grid\ThirdParty\Collection;

/**
 * Class BaseCollectionPlugin
 * @package Aheadworks\Ca\Plugin\Model\ResourceModel\Grid\CompanyColumn
 */
class BaseCollectionPlugin
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(
        Collection $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * Before load plugin
     *
     * @param AbstractDb $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(
        $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        $this->collection->joinFieldsBeforeLoad($subject);

        return [$printQuery, $logQuery];
    }

    /**
     * Around addFieldToFilter plugin
     *
     * @param AbstractDb $subject
     * @param \Closure $proceed
     * @param $field
     * @param null $condition
     * @return AbstractDb
     */
    public function aroundAddFieldToFilter(
        $subject,
        \Closure $proceed,
        $field,
        $condition = null
    ) {
        $this->collection->addFieldToFilter($subject, $field);

        return $proceed($field, $condition);
    }
}
