<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Source\Role\Permission;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\Ca\Model\Source\Role\Permission
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Permission type list
     */
    const ALLOW = 'allow';
    const DENY = 'deny';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ALLOW,
                'label' => __('Allow')
            ],
            [
                'value' => self::DENY,
                'label' => __('Deny')
            ]
        ];
    }
}
