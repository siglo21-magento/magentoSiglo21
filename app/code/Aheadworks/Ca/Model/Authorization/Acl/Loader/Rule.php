<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Acl\Loader;

use Magento\Framework\Acl\LoaderInterface;
use Magento\Framework\Acl;
use Aheadworks\Ca\Model\Source\Role\Permission\Type;

/**
 * Class Rule
 * @package Aheadworks\Ca\Model\Authorization\Acl\Loader
 */
class Rule extends Role implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function populateAcl(Acl $acl)
    {
        foreach ($this->getCompanyRoles() as $role) {
            $roleId = $role->getId();

            foreach ($role->getPermissions() as $permission) {
                $resource = $permission->getResourceId();
                if ($acl->has($resource)) {
                    if ($permission->getPermission() == Type::ALLOW) {
                        if ($resource === $this->rootResource->getId()) {
                            $acl->allow($roleId);
                        }
                        $acl->allow($roleId, $resource);
                    } elseif ($permission->getPermission() == Type::DENY) {
                        $acl->deny($roleId, $resource);
                    }
                }
            }
        }
    }
}
