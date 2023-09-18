<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\Company;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Information
 *
 * @package Aheadworks\Ca\ViewModel\Company
 */
class Company implements ArgumentInterface
{
    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var CompanyInterfaceFactory
     */
    private $companyFactory;

    /**
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanyInterfaceFactory $companyFactory
     */
    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        CompanyInterfaceFactory $companyFactory
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyFactory = $companyFactory;
    }

    /**
     * Get company by customer
     *
     * @param CustomerInterface $customer
     * @return CompanyInterface|null
     * @throws NoSuchEntityException
     */
    public function getCompanyByCustomer($customer)
    {
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
            $company = $this->companyRepository->get($companyUser->getCompanyId());
        } else {
            $company = $this->companyFactory->create();
        }

        return $company;
    }
}
