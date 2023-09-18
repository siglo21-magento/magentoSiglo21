<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\GroupInterface;

/**
 * Class Group
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class Group extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ca_group';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, GroupInterface::ID);
    }
}
