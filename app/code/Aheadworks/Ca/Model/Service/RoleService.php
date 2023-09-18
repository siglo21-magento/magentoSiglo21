<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\AclManagementInterface;
use Aheadworks\Ca\Api\Data\RoleInterfaceFactory;
use Aheadworks\Ca\Api\Data\RolePermissionInterface;
use Aheadworks\Ca\Api\Data\RolePermissionInterfaceFactory;
use Aheadworks\Ca\Api\RoleManagementInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Model\Source\Role\Permission\Type;

/**
 * Class RoleService
 * @package Aheadworks\Ca\Model\Service
 */
class RoleService implements RoleManagementInterface
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var AclManagementInterface
     */
    private $aclManagement;

    /**
     * @var RolePermissionInterfaceFactory
     */
    private $rolePermissionFactory;

    /**
     * @var RoleInterfaceFactory
     */
    private $roleFactory;

    /**
     * @param AclManagementInterface $aclManagement
     * @param RoleRepositoryInterface $roleRepository
     * @param RolePermissionInterfaceFactory $rolePermissionFactory
     * @param RoleInterfaceFactory $roleFactory
     */
    public function __construct(
        AclManagementInterface $aclManagement,
        RoleRepositoryInterface $roleRepository,
        RolePermissionInterfaceFactory $rolePermissionFactory,
        RoleInterfaceFactory $roleFactory
    ) {
        $this->aclManagement = $aclManagement;
        $this->roleFactory = $roleFactory;
        $this->roleRepository = $roleRepository;
        $this->rolePermissionFactory = $rolePermissionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRole($role, $postedResources)
    {
        $aclResources = $this->aclManagement->getResourceKeys();
        $permissions = [];
        if (count($aclResources) == count($postedResources)) {
            /** @var RolePermissionInterface $permission */
            $permission = $this->rolePermissionFactory->create();
            $permission
                ->setPermission(Type::ALLOW)
                ->setResourceId($this->aclManagement->getRootResourceId());
            $permissions[] = $permission;
        } else {
            foreach ($aclResources as $resourceId) {
                /** @var RolePermissionInterface $permission */
                $permission = $this->rolePermissionFactory->create();
                $permission
                    ->setPermission(in_array($resourceId, $postedResources) ? Type::ALLOW : Type::DENY)
                    ->setResourceId($resourceId);
                $permissions[] = $permission;
            }
        }
        $role->setPermissions($permissions);

        return $this->roleRepository->save($role);
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultRole($companyId)
    {
        $role = $this->roleFactory
            ->create()
            ->setName('Administrator')
            ->setCompanyId($companyId);
        $postedResources = $this->aclManagement->getResourceKeys();

        return $this->saveRole($role, $postedResources);
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultUserRole($companyId)
    {
        $role = $this->roleFactory
            ->create()
            ->setName('New User')
            ->setIsDefault(true)
            ->setCompanyId($companyId);
        $postedResources = [
            'Aheadworks_Ca::all',
            'Aheadworks_Ca::companies',
            'Aheadworks_Ca::companies_view'
        ];

        return $this->saveRole($role, $postedResources);
    }
}
