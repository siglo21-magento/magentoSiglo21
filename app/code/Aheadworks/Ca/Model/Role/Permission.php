<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Role;

use Aheadworks\Ca\Api\Data\RolePermissionInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Permission
 * @package Aheadworks\Ca\Model\Role
 */
class Permission extends AbstractModel implements RolePermissionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getResourceId()
    {
        return $this->getData(self::RESOURCE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setResourceId($resourceId)
    {
        return $this->setData(self::RESOURCE_ID, $resourceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission()
    {
        return $this->getData(self::PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($permission)
    {
        return $this->setData(self::PERMISSION, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\RolePermissionExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
