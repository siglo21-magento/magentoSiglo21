<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\Adminhtml\Customer;

use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status as CompanyUserStatusSource;

/**
 * Class CompanyInformation
 * @package Aheadworks\Ca\ViewModel\Adminhtml\Customer
 */
class CompanyInformation implements ArgumentInterface
{
    /**
     * @var CustomerLocator
     */
    private $locator;

    /**
     * @var CompanyUserStatusSource
     */
    private $companyUserStatusSource;

    /**
     * @var SellerCompanyManagementInterface
     */
    private $companyManagement;

    /**
     * @param CustomerLocator $locator
     * @param CompanyUserStatusSource $companyUserStatusSource
     * @param SellerCompanyManagementInterface $companyManagement
     */
    public function __construct(
        CustomerLocator $locator,
        CompanyUserStatusSource $companyUserStatusSource,
        SellerCompanyManagementInterface $companyManagement
    ) {
        $this->locator = $locator;
        $this->companyUserStatusSource = $companyUserStatusSource;
        $this->companyManagement = $companyManagement;
    }

    /**
     * Retrieve customer job position
     *
     * @return string
     */
    public function getJobPosition()
    {
        $jobPosition = '';
        $customer = $this->locator->getCustomer();

        if ($customer && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $jobPosition = $customer->getExtensionAttributes()->getAwCaCompanyUser()->getJobTitle();
        }

        return $jobPosition;
    }

    /**
     * Retrieve customer status in company
     *
     * @return string
     */
    public function getStatusInCompany()
    {
        $statusInCompany = '';
        $customer = $this->locator->getCustomer();

        if ($customer && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $statusInCompany = $customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsActivated();
            $statusInCompany = $this->companyUserStatusSource->getStatusLabel($statusInCompany);
        }

        return $statusInCompany;
    }

    /**
     * Retrieve company name
     *
     * @return string
     */
    public function getCompanyName()
    {
        $companyName = '';
        $customer = $this->locator->getCustomer();

        if ($customer) {
            $company = $this->companyManagement->getCompanyByCustomerId($customer->getId());
            if ($company) {
                $companyName = $company->getName();
            }
        }

        return $companyName;
    }
}
