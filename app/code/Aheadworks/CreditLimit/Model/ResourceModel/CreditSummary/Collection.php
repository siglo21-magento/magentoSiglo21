<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary as CreditSummarySummary;
use Aheadworks\CreditLimit\Model\CreditSummary;

/**
 * Class Collection
 *
 * @see \Aheadworks\CreditLimit\Model\ResourceModel\Customer\Collection used as base collection
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(CreditSummary::class, CreditSummarySummary::class);
    }
}
