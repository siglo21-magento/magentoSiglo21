<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\GroupManagementInterface;
use Aheadworks\Ca\Api\RoleManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\Source\Company\Status;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CompanyManagement
 * @package Aheadworks\Ca\Model\Source\Company
 */
class CompanyManagement
{
    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Company
     */
    private $resourceModel;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var ExtensionAttributesBuilder
     */
    private $companyUserExtensionAttributesBuilder;

    /**
     * @param CompanyRepositoryInterface $companyRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param Company $resourceModel
     * @param GroupManagementInterface $groupManagement
     * @param RoleManagementInterface $roleManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param Notifier $notifier
     * @param ExtensionAttributesBuilder $extensionAttributesBuilder
     */
    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        CustomerRepositoryInterface $customerRepository,
        Company $resourceModel,
        GroupManagementInterface $groupManagement,
        RoleManagementInterface $roleManagement,
        CompanyUserManagementInterface $companyUserManagement,
        Notifier $notifier,
        ExtensionAttributesBuilder $extensionAttributesBuilder
    ) {
        $this->companyRepository = $companyRepository;
        $this->customerRepository = $customerRepository;
        $this->resourceModel = $resourceModel;
        $this->groupManagement = $groupManagement;
        $this->roleManagement = $roleManagement;
        $this->companyUserManagement = $companyUserManagement;
        $this->notifier = $notifier;
        $this->companyUserExtensionAttributesBuilder = $extensionAttributesBuilder;
    }

    /**
     * Create company
     *
     * @param CompanyInterface $company
     * @param CustomerInterface $customer
     * @return CompanyInterface
     * @throws Exception
     */
    public function createCompany(CompanyInterface $company, CustomerInterface $customer)
    {
        $this->resourceModel->beginTransaction();
        try {
            // create root group
            $group = $this->groupManagement->createDefaultGroup();

            // create company
            $company->setRootGroupId($group->getId());
            $this->companyRepository->save($company);

            // create default role
            $rootRole = $this->roleManagement->createDefaultRole($company->getId());
            // create default user role
            $userRole = $this->roleManagement->createDefaultUserRole($company->getId());

            // create root customer
            $customer = $this->createAndSaveDefaultCustomer(
                $customer,
                $company,
                $group,
                $rootRole
            );

            $this->resourceModel->commit();
        } catch (Exception $e) {
            $this->resourceModel->rollBack();
            throw $e;
        }

        if ($company->getStatus() == Status::PENDING_APPROVAL) {
            $this->notifier->notifyOnNewCompanyCreated($company);
        } else {
            $this->notifier->notifyOnCompanyStatusChange($company, null);
        }

        return $company;
    }

    /**
     * Update company
     *
     * @param CompanyInterface $company
     * @param CustomerInterface $customer
     * @return CompanyInterface
     * @throws Exception
     */
    public function updateCompany(CompanyInterface $company, CustomerInterface $customer)
    {
        $this->resourceModel->beginTransaction();
        try {
            $this->companyUserManagement->saveUser($customer);
            $oldCompany = $this->companyRepository->get($company->getId());
            $this->companyRepository->save($company);
            $this->resourceModel->commit();

            $this->notifier->notifyOnCompanyStatusChange($company, $oldCompany->getStatus());
            $this->updateGroupForAllCompanyUsers($company, $oldCompany);
        } catch (Exception $e) {
            $this->resourceModel->rollBack();
            throw $e;
        }

        return $company;
    }

    /**
     * Check if company blocked
     *
     * @param $companyId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isBlocked($companyId)
    {
        $company = $this->companyRepository->get($companyId);

        return $company->getStatus() != Status::APPROVED;
    }

    /**
     * Change company status
     *
     * @param int $companyId
     * @param string $status
     * @return bool
     */
    public function changeStatus($companyId, $status)
    {
        try {
            $company = $this->companyRepository->get($companyId);
            $oldStatus = $company->getStatus();
            $company->setStatus($status);
            $this->companyRepository->save($company);
            $this->notifier->notifyOnCompanyStatusChange($company, $oldStatus);

            $result = true;
        } catch (\Exception $exception) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $customerId
     * @return CompanyInterface|null
     */
    public function getCompanyByCustomerId($customerId)
    {
        $company = null;
        try {
            $customer = $this->customerRepository->getById($customerId);
            if ($customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $companyId = $customer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
                $company = $this->companyRepository->get($companyId);
            }
        } catch (\Exception $e) {
        }

        return $company;
    }

    /**
     * Create root customer
     *
     * @param CustomerInterface $customer
     * @param CompanyInterface $company
     * @param GroupInterface $group
     * @param RoleInterface $role
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createAndSaveDefaultCustomer(
        CustomerInterface $customer,
        CompanyInterface $company,
        GroupInterface $group,
        RoleInterface $role
    ) {
        $this->companyUserExtensionAttributesBuilder->setAwCompanyUserIfNotIsset($customer);

        $customer->getExtensionAttributes()->getAwCaCompanyUser()
            ->setCompanyId($company->getId())
            ->setIsRoot(true)
            ->setCompanyGroupId($group->getId())
            ->setCompanyRoleId($role->getId());

        return $this->companyUserManagement->saveUser($customer);
    }

    /**
     * Update group for company users
     *
     * @param CompanyInterface $company
     * @param CompanyInterface $oldCompany
     * @return void
     */
    private function updateGroupForAllCompanyUsers($company, $oldCompany)
    {
        if ($company->getCustomerGroupId() != $oldCompany->getCustomerGroupId()) {
            $groupId = $company->getCustomerGroupId();

            $customers = $this->companyUserManagement->getAllUserForCompany($company->getId());

            foreach ($customers as $customer) {
                $customer->setGroupId($groupId);
                try {
                    $this->companyUserManagement->saveUser($customer);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }
}
