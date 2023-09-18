<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\Company;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class Customer
 *
 * @package Aheadworks\Ca\ViewModel\Company
 */
class Customer implements ArgumentInterface
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
     * Get current company user
     *
     * @return CustomerInterface
     */
    public function getCurrentCompanyUser()
    {
        return $this->companyUserManagement->getCurrentUser();
    }

    /**
     * Get root company user
     *
     * @param int $companyId
     * @return CustomerInterface
     */
    public function getRootCompanyUser($companyId)
    {
        return $this->companyUserManagement->getRootUserForCompany($companyId);
    }
}
