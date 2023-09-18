<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\CompanyUser\Form\Modifier;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Source\Role\Role as RoleSource;

/**
 * Class Role
 * @package Aheadworks\Ca\Ui\DataProvider\CompanyUser\Form\Modifier
 */
class Role implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RoleSource
     */
    private $roleSource;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param ArrayManager $arrayManager
     * @param RoleSource $roleSource
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        ArrayManager $arrayManager,
        RoleSource $roleSource,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->arrayManager = $arrayManager;
        $this->roleSource = $roleSource;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $rolePath = $this->arrayManager->findPath('role', $meta);
        $roleOptions = $this->createOptionArray();

        $isDefaultRoleId = '';
        foreach ($roleOptions as $option) {
            if ($option['is_default']) {
                $isDefaultRoleId = $option['value'];
            }
        }

        $role = [
            'options' => $roleOptions,
            'default' => $isDefaultRoleId
        ];
        $meta = $this->arrayManager->merge($rolePath, $meta, $role);

        return $meta;
    }

    /**
     * Retrieve roles as option array
     *
     * @return array
     */
    public function createOptionArray()
    {
        if ($user = $this->companyUserManagement->getCurrentUser()) {
            $companyId = $user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
            $this->roleSource->getSearchCriteriaBuilder()->addFilter(RoleInterface::COMPANY_ID, $companyId);
        }
        return $this->roleSource->toOptionArray();
    }
}
