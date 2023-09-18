<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface RolePermissionInterface
 * @api
 */
interface RolePermissionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const RESOURCE_ID = 'resource_id';
    const PERMISSION = 'permission';
    /**#@-*/

    /**
     * Get resource id
     *
     * @return string
     */
    public function getResourceId();

    /**
     * Set resource id
     *
     * @param string|null $resourceId
     * @return $this
     */
    public function setResourceId($resourceId);

    /**
     * Get permission
     *
     * @return string
     */
    public function getPermission();

    /**
     * Set permission
     *
     * @param string|null $permission
     * @return $this
     */
    public function setPermission($permission);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\RolePermissionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\RolePermissionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\RolePermissionExtensionInterface $extensionAttributes
    );
}
