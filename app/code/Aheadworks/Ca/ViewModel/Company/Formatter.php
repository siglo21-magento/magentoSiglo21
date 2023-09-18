<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\Company;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Company\Address\Renderer as AddressRenderer;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\Magento\ModuleUser\UserRepository;

/**
 * Class Formatter
 *
 * @package Aheadworks\Ca\ViewModel\Company
 */
class Formatter implements ArgumentInterface
{
    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param AddressRenderer $addressRenderer
     * @param UserRepository $userRepository
     */
    public function __construct(
        AddressRenderer $addressRenderer,
        UserRepository $userRepository
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->userRepository = $userRepository;
    }

    /**
     * Format company name
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function formatCompanyName($company)
    {
        $companyName = $company->getName();
        $companyName .= (!empty($company->getLegalName()))
            ? ' (' . ($company->getLegalName()) . ')'
            : '';

        return $companyName;
    }

    /**
     * Format company legal address
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function formatAddress($company)
    {
        return $this->addressRenderer->renderAddressFromCompany($company);
    }

    /**
     * Format company administrator name
     *
     * @param CustomerInterface $customer
     * @return string
     */
    public function formatCompanyAdministratorName($customer)
    {
        return $customer->getFirstname() . ' ' . $customer->getLastname();
    }

    /**
     * Format company administrator job title
     *
     * @param CustomerInterface $customer
     * @return string
     */
    public function getCompanyAdministratorJobTitle($customer)
    {
        $jobTitle = '';
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $jobTitle = $customer->getExtensionAttributes()->getAwCaCompanyUser()->getJobTitle();
        }

        return $jobTitle;
    }

    /**
     * Format company administrator phone number
     *
     * @param CustomerInterface $customer
     * @return string
     */
    public function getCompanyAdministratorTelephone($customer)
    {
        $telephone = '';
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $telephone = $customer->getExtensionAttributes()->getAwCaCompanyUser()->getTelephone();
        }

        return $telephone;
    }

    /**
     * Form sales representative name
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function formatSalesRepresentativeName($company)
    {
        try {
            $user = $this->userRepository->getById($company->getSalesRepresentativeId());
            $name = $user->getFirstName() . ' ' . $user->getLastName();
        } catch (NoSuchEntityException $exception) {
            $name = '';
        }

        return $name;
    }

    /**
     * Get sales representative email
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function getSalesRepresentativeEmail($company)
    {
        try {
            $user = $this->userRepository->getById($company->getSalesRepresentativeId());
            $email = $user->getEmail();
        } catch (NoSuchEntityException $exception) {
            $email = '';
        }

        return $email;
    }
}
