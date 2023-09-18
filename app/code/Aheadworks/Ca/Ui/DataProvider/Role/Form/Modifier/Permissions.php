<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\Data\RolePermissionInterface;
use Aheadworks\Ca\Model\Source\Role\Permission\Type;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Ca\ViewModel\Role\Role as RoleViewModel;

/**
 * Class Permissions
 * @package Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier
 */
class Permissions implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RoleViewModel
     */
    private $roleViewModel;

    /**
     * @param ArrayManager $arrayManager
     * @param RoleViewModel $roleViewModel
     */
    public function __construct(
        ArrayManager $arrayManager,
        RoleViewModel $roleViewModel
    ) {
        $this->arrayManager = $arrayManager;
        $this->roleViewModel = $roleViewModel;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $permissions = isset($data[RoleInterface::PERMISSIONS])
            ? $data[RoleInterface::PERMISSIONS]
            : [];

        $permissionsArray = [];
        /** @var RolePermissionInterface $permission */
        foreach ($permissions as $permission) {
            if ($permission->getPermission() == Type::ALLOW) {
                $permissionsArray[] = $permission->getResourceId();
            }
        }

        $data[RoleInterface::PERMISSIONS] = $permissionsArray;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $permissionsPath = $this->arrayManager->findPath('permissions', $meta);
        if ($permissionsPath) {
            $permissionsConfig['treeConfig']['core']['data'] = $this->roleViewModel->getRoleTree();
            $meta = $this->arrayManager->merge($permissionsPath, $meta, $permissionsConfig);
        }

        return $meta;
    }
}
