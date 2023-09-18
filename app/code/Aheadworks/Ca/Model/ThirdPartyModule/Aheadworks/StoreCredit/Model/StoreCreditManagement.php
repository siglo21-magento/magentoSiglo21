<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class StoreCreditManagement
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model
 */
class StoreCreditManagement
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param RoleRepositoryInterface $roleRepository
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        AuthorizationManagementInterface $authorizationManagement,
        RoleRepositoryInterface $roleRepository,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->authorizationManagement = $authorizationManagement;
        $this->roleRepository = $roleRepository;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Change customer id if needed
     *
     * @param int $customerId
     * @return int|null
     */
    public function changeCustomerIdIfNeeded($customerId)
    {
        $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);
        if ($rootCustomer) {
            $customerId = $rootCustomer->getId();
        }
        return $customerId;
    }

    /**
     * Change customer id if needed
     *
     * @param int $customerId
     * @return int|null
     */
    public function changeCustomerIdIfNeededForTransaction($customerId)
    {
        if ($this->isAvailableTransactions()) {
            $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);
            if ($rootCustomer) {
                $customerId = $rootCustomer->getId();
            }
        }
        return $customerId;
    }

    /**
     * Check if available transactions
     *
     * @return bool
     */
    public function isAvailableTransactions()
    {
        return $this->authorizationManagement->isAllowedByResource('Aheadworks_StoreCredit::company_stc_transactions');
    }

    /**
     * Check if available view and use
     *
     * @return bool
     */
    public function isAvailableViewAndUse()
    {
        return $this->authorizationManagement->isAllowedByResource('Aheadworks_StoreCredit::company_stc_view_and_use');
    }

    /**
     * Check if available subscribe options
     *
     * @return bool
     */
    public function isAvailableSubscribeOptions()
    {
        $result = true;
        if ($currentCompanyUser = $this->companyUserManagement->getCurrentUser()) {
            $result = $currentCompanyUser->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot();
        }

        return $result;
    }

    /**
     * Retrieve root user by customer id
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getRootUserByCustomerId($customerId)
    {
        return $this->companyUserManagement->getRootUserForCustomer($customerId);
    }

    /**
     * Change customer store credit details if needed
     *
     * @param \Aheadworks\StoreCredit\Api\Data\CustomerStoreCreditDetailsInterface $customerStcDetails
     * @return \Aheadworks\StoreCredit\Api\Data\CustomerStoreCreditDetailsInterface
     */
    public function changeCustomerStcDetailsIfNeeded($customerStcDetails)
    {
        $baseAmount = 0;
        if ($currentCompanyUser = $this->companyUserManagement->getCurrentUser()) {
            if ($this->isAvailableViewAndUse()) {
                $roleId = $currentCompanyUser->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyRoleId();
                try {
                    $role = $this->roleRepository->get($roleId);
                    $baseAmount = !empty($role->getAwStcBaseAmountLimit())
                        ? min($role->getAwStcBaseAmountLimit(), $customerStcDetails->getCustomerStoreCreditBalance())
                        : $customerStcDetails->getCustomerStoreCreditBalance();
                } catch (\Exception $e) {
                }
            }
            $customerStcDetails
                ->setCustomerStoreCreditBalance($baseAmount)
                ->setCustomerStoreCreditBalanceCurrency($this->priceCurrency->convertAndRound($baseAmount));
        }
        return $customerStcDetails;
    }
}
