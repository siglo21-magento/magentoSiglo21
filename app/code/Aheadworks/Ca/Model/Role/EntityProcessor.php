<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Role;

use Aheadworks\Ca\Model\Role as RoleModel;

/**
 * Class EntityProcessor
 * @package Aheadworks\Ca\Model\Role
 */
class EntityProcessor
{
    /**
     * @var array[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity data before save
     *
     * @param RoleModel $object
     * @return RoleModel
     */
    public function prepareDataBeforeSave($object)
    {
        foreach ($this->processors as $processor) {
            $processor->beforeSave($object);
        }
        return $object;
    }

    /**
     * Prepare entity data after load
     *
     * @param RoleModel $object
     * @return RoleModel
     */
    public function prepareDataAfterLoad($object)
    {
        foreach ($this->processors as $processor) {
            $processor->afterLoad($object);
        }
        return $object;
    }
}
