<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Aheadworks\Ca\Api\Data\CompanyUserInterfaceFactory;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class ExtensionAttributesManagement
 * @package Aheadworks\Ca\Model\Customer
 */
class ExtensionAttributesBuilder
{
    /**
     * @var CustomerExtensionFactory
     */
    private $customerExtensionFactory;

    /**
     * @var CompanyUserInterfaceFactory
     */
    private $companyUserFactory;

    /**
     * ExtensionAttributesManagement constructor.
     * @param CustomerExtensionFactory $customerExtensionFactory
     * @param CompanyUserInterfaceFactory $companyUserFactory
     */
    public function __construct(
        CustomerExtensionFactory $customerExtensionFactory,
        CompanyUserInterfaceFactory $companyUserFactory
    ) {
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->companyUserFactory = $companyUserFactory;
    }

    /**
     * Set extension attributes if not isset
     *
     * @param CustomerInterface $customer
     */
    public function setExtensionAttributesIfNotIsset($customer)
    {
        if (!$customer->getExtensionAttributes()) {
            $extensionAttributes = $this->customerExtensionFactory->create();
            $customer->setExtensionAttributes($extensionAttributes);
        }
    }

    /**
     * Set AW CompanyUser attributes if not isset
     *
     * @param CustomerInterface $customer
     */
    public function setAwCompanyUserIfNotIsset($customer)
    {
        $this->setExtensionAttributesIfNotIsset($customer);
        if (!$customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $awCompanyUser = $this->companyUserFactory->create();
            $customer->getExtensionAttributes()->setAwCaCompanyUser($awCompanyUser);
        }
    }
}
