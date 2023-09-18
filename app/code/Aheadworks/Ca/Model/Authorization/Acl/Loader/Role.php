<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Acl\Loader;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Magento\Framework\Acl\LoaderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Authorization\Model\Acl\Role\UserFactory;
use Magento\Framework\Acl;
use Magento\Framework\Acl\RootResource;

/**
 * Class Role
 * @package Aheadworks\Ca\Model\Authorization\Acl\Loader
 */
class Role implements LoaderInterface
{
    /**
     * @var RoleRepositoryInterface
     */
    protected $roleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var UserFactory
     */
    protected $roleFactory;

    /**
     * @var RootResource
     */
    protected $rootResource;

    /**
     * @var CompanyUserManagementInterface
     */
    protected $companyUserManagement;

    /**
     * @var RoleInterface[]|null
     */
    protected $companyRoles;

    /**
     * @param RoleRepositoryInterface $roleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UserFactory $roleFactory
     * @param RootResource $rootResource
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UserFactory $roleFactory,
        RootResource $rootResource,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->roleRepository = $roleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->roleFactory = $roleFactory;
        $this->rootResource = $rootResource;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function populateAcl(Acl $acl)
    {
        foreach ($this->getCompanyRoles() as $role) {
            $roleId = $role->getId();
            $acl->addRole($this->roleFactory->create(['roleId' => $roleId]));
        }
    }

    /**
     * Retrieve company roles
     *
     * @return RoleInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCompanyRoles()
    {
        if ($this->companyRoles === null) {
            $this->companyRoles = [];
            if ($user = $this->companyUserManagement->getCurrentUser()) {
                $companyId = $user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
                $this->searchCriteriaBuilder
                    ->addFilter(RoleInterface::COMPANY_ID, $companyId);
                $this->companyRoles = $this->roleRepository
                    ->getList($this->searchCriteriaBuilder->create())
                    ->getItems();
            }
        }
        return $this->companyRoles;
    }
}
