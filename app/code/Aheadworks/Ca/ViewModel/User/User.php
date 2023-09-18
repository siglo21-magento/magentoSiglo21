<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\User;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Model\Url;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\Status as StatusSource;

/**
 * Class User
 * @package Aheadworks\Ca\ViewModel\User
 */
class User implements ArgumentInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * @param Url $url
     * @param RoleRepositoryInterface $roleRepository
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param StatusSource $statusSource
     */
    public function __construct(
        Url $url,
        RoleRepositoryInterface $roleRepository,
        CompanyUserManagementInterface $companyUserManagement,
        StatusSource $statusSource
    ) {
        $this->url = $url;
        $this->roleRepository = $roleRepository;
        $this->companyUserManagement = $companyUserManagement;
        $this->statusSource = $statusSource;
    }

    /**
     * Retrieve role name by role id
     *
     * @param $roleId
     * @return string
     */
    public function getRoleName($roleId)
    {
        try {
            $role = $this->roleRepository->get($roleId);
        } catch (NoSuchEntityException $e) {
            return '';
        }

        return $role->getName();
    }

    /**
     * Get status label
     *
     * @param int $status
     * @return string
     */
    public function getStatusLabel($status)
    {
        return $this->statusSource->getStatusLabel($status);
    }

    /**
     * Retrieve edit url
     *
     * @param int $customerId
     * @return string
     */
    public function getEditUrl($customerId)
    {
        return $this->url->getFrontendEditCustomerUrl($customerId);
    }

    /**
     * Retrieve customer change status url
     *
     * @param int $customerId
     * @param bool $needActivate
     * @return string
     */
    public function getChangeStatusUrl($customerId, $needActivate)
    {
        return $this->url->getFrontendCustomerChangeStatusUrl($customerId, $needActivate);
    }

    /**
     * Check if given customer id belong current customer
     *
     * @param int $customerId
     * @return bool
     */
    public function isCurrentCompanyUser($customerId)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        return $currentUser->getId() == $customerId;
    }

    /**
     * Check if customer is root
     *
     * @param CustomerInterface $customer
     * @return boolean
     */
    public function isRoot($customer)
    {
        return (bool)$customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot();
    }

    /**
     * Check if customer is activated
     *
     * @param CustomerInterface $customer
     * @return boolean
     */
    public function isActivated($customer)
    {
        return (bool)$customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsActivated();
    }
}
