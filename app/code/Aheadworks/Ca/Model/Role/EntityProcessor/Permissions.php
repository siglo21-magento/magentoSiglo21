<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Role\EntityProcessor;

use Aheadworks\Ca\Api\AclManagementInterface;
use Aheadworks\Ca\Api\Data\RolePermissionInterface;
use Aheadworks\Ca\Model\Role as RoleModel;
use Aheadworks\Ca\Model\Source\Role\Permission\Type;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Serialize\Serializer\Json;
use Aheadworks\Ca\Api\Data\RolePermissionInterfaceFactory;

/**
 * Class Permissions
 * @package Aheadworks\Ca\Model\Role\EntityProcessor
 */
class Permissions
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var RolePermissionInterfaceFactory
     */
    private $rolePermissionFactory;

    /**
     * @var AclManagementInterface
     */
    private $aclManagement;

    /**
     * @param Json $serializer
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RolePermissionInterfaceFactory $rolePermissionFactory
     * @param AclManagementInterface $aclManagement
     */
    public function __construct(
        Json $serializer,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        RolePermissionInterfaceFactory $rolePermissionFactory,
        AclManagementInterface $aclManagement
    ) {
        $this->serializer = $serializer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->rolePermissionFactory = $rolePermissionFactory;
        $this->aclManagement = $aclManagement;
    }

    /**
     * Convert permissions data before save
     *
     * @param RoleModel $object
     * @return RoleModel
     */
    public function beforeSave($object)
    {
        if (is_array($object->getPermissions())) {
            $permissions = $object->getPermissions();
            $permissionsArray = [];
            foreach ($permissions as $permission) {
                $permissionsArray[] = $this->dataObjectProcessor
                    ->buildOutputDataArray($permission, RolePermissionInterface::class);
            }
            $object->setPermissions($this->serializer->serialize($permissionsArray));
        }

        return $object;
    }

    /**
     * Convert permissions data after load
     *
     * @param RoleModel $object
     * @return RoleModel
     */
    public function afterLoad($object)
    {
        if ($object->getPermissions()) {
            $permissionsArray = $this->serializer->unserialize($object->getPermissions());
            $permissions = [];
            foreach ($permissionsArray as $permission) {
                $permissions[] = $this->createObject($permission);
            }

            $resourceKeys = $this->aclManagement->getResourceKeys();
            /** @var RolePermissionInterface $firstPermission */
            $firstPermission = reset($permissions);
            if (count($permissions) == 1
                && $firstPermission->getResourceId() == $this->aclManagement->getRootResourceId()
            ) {
                $preparedPermissions = [];
                /** @var RolePermissionInterface $permission */
                foreach ($resourceKeys as $resourceId) {
                    $permission = [
                        RolePermissionInterface::RESOURCE_ID => $resourceId,
                        RolePermissionInterface::PERMISSION => Type::ALLOW
                    ];
                    $preparedPermissions[] = $this->createObject($permission);
                }
            } else {
                $preparedPermissions = $permissions;
                /** @var RolePermissionInterface $permission */
                foreach ($resourceKeys as $resourceId) {
                    if (!$this->find($permissions, $resourceId)) {
                        $permission = [
                            RolePermissionInterface::RESOURCE_ID => $resourceId,
                            RolePermissionInterface::PERMISSION => Type::DENY
                        ];
                        $preparedPermissions[] = $this->createObject($permission);
                    }
                }
            }

            $object->setPermissions($preparedPermissions);
        }

        return $object;
    }

    /**
     * Create permission object
     *
     * @param array $permission
     * @return RolePermissionInterface
     */
    private function createObject($permission)
    {
        $permissionObject = $this->rolePermissionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $permissionObject,
            $permission,
            RolePermissionInterface::class
        );
        return $permissionObject;
    }

    /**
     * Find $resourceId in $permissions array
     *
     * @param array $permissions
     * @param string $resourceId
     * @return bool
     */
    private function find($permissions, $resourceId)
    {
        /** @var RolePermissionInterface $permission */
        foreach ($permissions as $permission) {
            if ($permission->getResourceId() == $resourceId) {
                return true;
            }
        }
        return false;
    }
}
