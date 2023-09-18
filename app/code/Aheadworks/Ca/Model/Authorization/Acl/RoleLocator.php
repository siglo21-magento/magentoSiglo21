<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Acl;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Authorization\RoleLocatorInterface;

/**
 * Class RoleLocator
 * @package Aheadworks\Ca\Model\Authorization\Authorization\Acl
 */
class RoleLocator implements RoleLocatorInterface
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclRoleId()
    {
        $roleId = 0;
        if ($user = $this->companyUserManagement->getCurrentUser()) {
            $roleId = $user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyRoleId();
        }
        
        return $roleId;
    }
}
