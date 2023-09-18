<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel\Group;

use Aheadworks\Ca\Model\Group;
use Aheadworks\Ca\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\ResourceModel\Group as GroupResource;

/**
 * Class Collection
 * @package Aheadworks\Ca\Model\ResourceModel\Group
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Group::class, GroupResource::class);
    }
}
