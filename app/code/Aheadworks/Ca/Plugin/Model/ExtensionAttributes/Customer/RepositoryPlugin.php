<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Model\ExtensionAttributes\Customer;

use Aheadworks\Ca\Model\Customer\CompanyUser\Repository;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface as Customer;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerRepositoryPlugin
 * @package Aheadworks\Ca\Plugin\Model\ExtensionAttributes
 */
class RepositoryPlugin
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
     * Save Extension attributes for customer
     *
     * @param CustomerRepositoryInterface $subject
     * @param Customer $result
     * @param Customer $customer
     * @return Customer
     */
    public function afterSave(
        CustomerRepositoryInterface $subject,
        Customer $result,
        Customer $customer
    ) {
        try {
            $this->companyUserExtensionAttributesBuilder->setExtensionAttributesIfNotIsset($customer);
            $this->companyUserExtensionAttributesBuilder->setExtensionAttributesIfNotIsset($result);

            if ($companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $companyUser->setCustomerId($result->getId());
                $this->companyUserRepository->save($companyUser);

                if (!$result->getExtensionAttributes()->getAwCaCompanyUser()) {
                    $result->getExtensionAttributes()->setAwCaCompanyUser($companyUser);
                }
            }
        } catch (CouldNotSaveException $e) {
        }

        return $result;
    }

    /**
     * Preserve delete Company root customer
     *
     * @param CustomerRepositoryInterface $subject
     * @param int $customerId
     * @return null
     * @throws \Exception
     */
    public function beforeDeleteById(
        CustomerRepositoryInterface $subject,
        $customerId
    ) {
        try {
            $customer = $subject->getById($customerId);
        } catch (\Exception $exception) {
            return null;
        }

        if ($companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            if ($companyUser->getIsRoot()) {
                throw new LocalizedException(__('Impossible to delete a company\'s admin'));
            }
        }

        return null;
    }
}
