<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model\Service;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\GroupRepositoryInterface;
use Aheadworks\Ca\Model\Customer\Checker\ConvertToCompanyAdmin\Checker as ConvertToCompanyAdminChecker;
use Aheadworks\Ca\Model\Customer\CompanyUser\Notifier;
use Aheadworks\Ca\Model\Customer\Checker\EmailAvailability\Checker as EmailAvailabilityChecker;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerExtension;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Customer\Api\Data\CustomerSearchResultsInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ca\Model\Service\CompanyUserService;
use Aheadworks\Ca\Api\Data\GroupInterface;

/**
 * Class CompanyUserServiceTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model\Service
 */
class CompanyUserServiceTest extends TestCase
{
    /**
     * @var CompanyUserService
     */
    private $model;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var GroupRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupRepositoryMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var AccountManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $accountManagementMock;

    /**
     * @var UserContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userContextMock;

    /**
     * @var EmailAvailabilityChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailAvailabilityCheckerMock;

    /**
     * @var ConvertToCompanyAdminChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $convertToCompanyAdminCheckerMock;

    /**
     * @var Notifier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $notifierMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->groupRepositoryMock = $this->getMockForAbstractClass(GroupRepositoryInterface::class);
        $this->customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);
        $this->accountManagementMock = $this->getMockForAbstractClass(AccountManagementInterface::class);
        $this->userContextMock = $this->getMockForAbstractClass(UserContextInterface::class);
        $this->emailAvailabilityCheckerMock = $this->createMock(EmailAvailabilityChecker::class);
        $this->convertToCompanyAdminCheckerMock = $this->createMock(ConvertToCompanyAdminChecker::class);
        $this->notifierMock = $this->createMock(Notifier::class);
        $this->model = $objectManager->getObject(
            CompanyUserService::class,
            [
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'groupRepository' => $this->groupRepositoryMock,
                'customerRepository' => $this->customerRepositoryMock,
                'accountManagement' => $this->accountManagementMock,
                'userContext' => $this->userContextMock,
                'emailAvailabilityChecker' => $this->emailAvailabilityCheckerMock,
                'convertToCompanyAdminChecker' => $this->convertToCompanyAdminCheckerMock,
                'notifier' => $this->notifierMock,
            ]
        );
    }

    /**
     * Test getCurrentUser method
     */
    public function testGetCurrentUser()
    {
        $userId = 5;
        $this->userContextMock->expects($this->once())
            ->method('getUserId')
            ->willReturn($userId);
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $companyUserMock = $this->getMockForAbstractClass(CompanyUserInterface::class);
        $customerExtensionMock = $this->createPartialMock(
            CustomerExtension::class,
            ['getAwCaCompanyUser']
        );

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($userId)
            ->willReturn($customerMock);
        $customerMock->expects($this->exactly(2))
            ->method('getExtensionAttributes')
            ->willReturn($customerExtensionMock);
        $customerExtensionMock->expects($this->once())
            ->method('getAwCaCompanyUser')
            ->willReturn($companyUserMock);

        $this->assertEquals($customerMock, $this->model->getCurrentUser());
    }

    /**
     * Test getCurrentUser method when customer is not a company user
     */
    public function testGetCurrentUserOnNotCompanyUser()
    {
        $userId = 3;
        $this->userContextMock->expects($this->once())
            ->method('getUserId')
            ->willReturn($userId);
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($userId)
            ->willReturn($customerMock);
        $customerMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn(null);

        $this->assertEquals(null, $this->model->getCurrentUser());
    }

    /**
     * Test getRootUserForCustomer method
     */
    public function testGetRootUserForCustomer()
    {
        $customerId = 5;
        $companyId = 1;
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $rootCustomerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);
        $customerExtensionMock = $this->createPartialMock(
            CustomerExtension::class,
            ['getAwCaCompanyUser']
        );
        $customerMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($customerExtensionMock);
        $companyUserMock = $this->getMockForAbstractClass(CompanyUserInterface::class);
        $customerExtensionMock->expects($this->once())
            ->method('getAwCaCompanyUser')
            ->willReturn($companyUserMock);
        $companyUserMock->expects($this->once())
            ->method('getCompanyId')
            ->willReturn($companyId);

        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive(['aw_ca_customer_by_company_id', $companyId], ['aw_ca_customer_is_root', null])
            ->willReturnSelf();
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);
        $searchResult = $this->getMockForAbstractClass(CustomerSearchResultsInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResult);
        $searchResult->expects($this->exactly(2))
            ->method('getItems')
            ->willReturn([$rootCustomerMock]);

        $this->assertEquals($rootCustomerMock, $this->model->getRootUserForCustomer($customerId));
    }

    /**
     * Test getRootUserForCustomer method in case no root found
     */
    public function testGetRootUserForCustomerOnNoRootFound()
    {
        $customerId = 3;
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);
        $customerExtensionMock = $this->createPartialMock(
            CustomerExtension::class,
            ['getAwCaCompanyUser']
        );
        $customerMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($customerExtensionMock);
        $customerExtensionMock->expects($this->once())
            ->method('getAwCaCompanyUser')
            ->willReturn(null);

        $this->assertEquals(null, $this->model->getRootUserForCustomer($customerId));
    }

    /**
     * Test saveUser method on Save
     */
    public function testSaveUserOnSave()
    {
        $userId = 3;
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($userId);
        $this->customerRepositoryMock->expects($this->once())
            ->method('save')
            ->with($customerMock)
            ->willReturn($customerMock);

        $this->assertEquals($customerMock, $this->model->saveUser($customerMock));
    }

    /**
     * Test saveUser method on create account
     */
    public function testSaveUserOnNewUser()
    {
        $userId = null;
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($userId);
        $this->accountManagementMock->expects($this->once())
            ->method('createAccount')
            ->with($customerMock)
            ->willReturn($customerMock);
        $customerExtensionMock = $this->createPartialMock(
            CustomerExtension::class,
            ['getAwCaCompanyUser']
        );
        $customerMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($customerExtensionMock);
        $companyUserMock = $this->getMockForAbstractClass(CompanyUserInterface::class);
        $customerExtensionMock->expects($this->once())
            ->method('getAwCaCompanyUser')
            ->willReturn($companyUserMock);
        $companyUserMock->expects($this->once())
            ->method('getIsRoot')
            ->willReturn(false);
        $this->notifierMock->expects($this->once())
            ->method('notifyOnNewUserCreated')
            ->with($customerMock);

        $this->assertEquals($customerMock, $this->model->saveUser($customerMock));
    }

    /**
     * Test getChildUsers method
     */
    public function testGetChildUsers()
    {
        $userId = 3;
        $childCustomerMock1 = $this->getMockForAbstractClass(CustomerInterface::class);
        $childCustomerMock2 = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->prepareChildCustomers($userId, $childCustomerMock1, $childCustomerMock2);
        $result = [$childCustomerMock1, $childCustomerMock2];

        $this->assertEquals($result, $this->model->getChildUsers($userId));
    }

    /**
     * Prepare child customers
     *
     * @param int $userId
     * @param \PHPUnit_Framework_MockObject_MockObject $childCustomerMock1
     * @param \PHPUnit_Framework_MockObject_MockObject $childCustomerMock2
     * @throws \ReflectionException
     */
    private function prepareChildCustomers($userId, $childCustomerMock1, $childCustomerMock2)
    {
        $groupId = 3;
        $path = '/10';
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $result = [$childCustomerMock1, $childCustomerMock2];
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($userId)
            ->willReturn($customerMock);
        $customerExtensionMock = $this->createPartialMock(
            CustomerExtension::class,
            ['getAwCaCompanyUser']
        );
        $customerMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($customerExtensionMock);
        $companyUserMock = $this->getMockForAbstractClass(CompanyUserInterface::class);
        $customerExtensionMock->expects($this->once())
            ->method('getAwCaCompanyUser')
            ->willReturn($companyUserMock);
        $companyUserMock->expects($this->once())
            ->method('getCompanyGroupId')
            ->willReturn($groupId);
        $group = $this->createMock(GroupInterface::class);
        $this->groupRepositoryMock->expects($this->once())
            ->method('get')
            ->with($groupId)
            ->willReturn($group);
        $group->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('aw_ca_customer_by_group_path', $path)
            ->willReturnSelf();
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);
        $searchResult = $this->getMockForAbstractClass(CustomerSearchResultsInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResult);
        $searchResult->expects($this->once())
            ->method('getItems')
            ->willReturn($result);
    }

    /**
     * Test getChildUsersIds method
     */
    public function testGetChildUsersIds()
    {
        $userId = 3;
        $childCustomerIds = [2,7];
        $childCustomerMock1 = $this->getMockForAbstractClass(CustomerInterface::class);
        $childCustomerMock2 = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->prepareChildCustomers($userId, $childCustomerMock1, $childCustomerMock2);

        $childCustomerMock1->expects($this->once())
            ->method('getId')
            ->willReturn($childCustomerIds[0]);
        $childCustomerMock2->expects($this->once())
            ->method('getId')
            ->willReturn($childCustomerIds[1]);

        $this->assertEquals($childCustomerIds, $this->model->getChildUsersIds($userId));
    }

    /**
     * Test isEmailAvailable method
     */
    public function testIsEmailAvailable()
    {
        $email = 'test@example.com';
        $websiteId = 1;
        $result = true;
        $this->emailAvailabilityCheckerMock->expects($this->once())
            ->method('check')
            ->with($email, $websiteId)
            ->willReturn($result);

        $this->assertEquals($result, $this->model->isEmailAvailable($email, $websiteId));
    }

    /**
     * Test isAvailableConvertToCompanyAdmin method
     */
    public function testIsAvailableConvertToCompanyAdmin()
    {
        $email = 'test@example.com';
        $websiteId = 1;
        $result = false;
        $this->convertToCompanyAdminCheckerMock->expects($this->once())
            ->method('check')
            ->with($email, $websiteId)
            ->willReturn($result);

        $this->assertEquals($result, $this->model->isAvailableConvertToCompanyAdmin($email, $websiteId));
    }
}
