<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Model\ExtensionAttributes\Customer;

use Aheadworks\Ca\Model\Customer\CompanyUser\Repository;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface as Customer;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;

/**
 * Class CustomerResourceModelPlugin
 * @package Aheadworks\Ca\Plugin\Model\ExtensionAttributes
 */
class ResourceModelPlugin
{
    /**
     * @var Repository
     */
    private $companyUserRepository;

    /**
     * @var CustomerExtensionFactory
     */
    private $customerExtensionFactory;

    /**
     * @var ExtensionAttributesBuilder
     */
    private $companyUserExtensionAttributesBuilder;

    /**
     * CustomerResourceModelPlugin constructor.
     * @param Repository $companyUserRepository
     * @param CustomerExtensionFactory $customerExtensionFactory
     * @param ExtensionAttributesBuilder $extensionAttributesBuilder
     */
    public function __construct(
        Repository $companyUserRepository,
        CustomerExtensionFactory $customerExtensionFactory,
        ExtensionAttributesBuilder $extensionAttributesBuilder
    ) {
        $this->companyUserRepository = $companyUserRepository;
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->companyUserExtensionAttributesBuilder = $extensionAttributesBuilder;
    }

    /**
     * Load Extension attributes for customer
     *
     * @param CustomerResourceModel $subject
     * @param CustomerResourceModel $result
     * @param Customer $customer
     * @return CustomerResourceModel
     */
    public function afterLoad(
        CustomerResourceModel $subject,
        CustomerResourceModel $result,
        $customer
    ) {
        if ($customer->getId()) {
            $this->companyUserExtensionAttributesBuilder->setExtensionAttributesIfNotIsset($customer);

            try {
                $companyUser = $this->companyUserRepository->get($customer->getId());
                $customer
                    ->getExtensionAttributes()
                    ->setAwCaCompanyUser($companyUser);
            } catch (\Exception $e) {
            }
        }

        return $subject;
    }
}
