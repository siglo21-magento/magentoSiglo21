<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model\Service;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Authorization\Acl\ResourceMapper;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\AuthorizationInterface;
use Aheadworks\Ca\Model\Authorization\CustomProcessor\ProcessorInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ca\Model\Service\AuthorizationService;

/**
 * Class AuthorizationServiceTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model\Service
 */
class AuthorizationServiceTest extends TestCase
{
    /**
     * @var AuthorizationService
     */
    private $model;

    /**
     * @var AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authorizationMock;

    /**
     * @var ResourceMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMapperMock;

    /**
     * @var UserContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userContextMock;

    /**
     * @var ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customProcessorMock;

    /**
     * @var CompanyUserManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $companyUserManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->authorizationMock = $this->getMockForAbstractClass(AuthorizationInterface::class);
        $this->resourceMapperMock = $this->createMock(ResourceMapper::class);
        $this->userContextMock = $this->getMockForAbstractClass(UserContextInterface::class);
        $this->customProcessorMock = $this->getMockForAbstractClass(ProcessorInterface::class);
        $this->companyUserManagementMock = $this->getMockForAbstractClass(CompanyUserManagementInterface::class);
        $this->model = $objectManager->getObject(
            AuthorizationService::class,
            [
                'authorization' => $this->authorizationMock,
                'resourceMapper' => $this->resourceMapperMock,
                'userContext' => $this->userContextMock,
                'customProcessor' => $this->customProcessorMock,
                'companyUserManagement' => $this->companyUserManagementMock,
            ]
        );
    }

    /**
     * Test isAllowed method
     */
    public function testIsAllowed()
    {
        $path = 'aw_ca/role/edit';
        $resource = 'Aheadworks_Ca::company_roles_edit';
        $result = true;
        $this->resourceMapperMock->expects($this->once())
            ->method('getResourceByPath')
            ->with($path)
            ->willReturn($resource);

        $this->initResource($resource, $result, true);

        $this->assertEquals($result, $this->model->isAllowed($path));
    }

    /**
     * Test isAllowedByResource method
     */
    public function testIsNotAllowedByResource()
    {
        $result = false;
        $resource = 'Aheadworks_Ca::company_roles_edit';

        $this->initResource($resource, false, !$result);

        $this->assertEquals($result, $this->model->isAllowedByResource($resource));
    }

    /**
     * Init resource
     *
     * @param string $resource
     * @param bool $authorizationResult
     * @param bool $customProcessorResult
     * @throws \ReflectionException
     */
    private function initResource($resource, $authorizationResult, $customProcessorResult)
    {
        $this->userContextMock->expects($this->once())
            ->method('getUserId')
            ->willReturn(3);
        $this->authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with($resource)
            ->willReturn($authorizationResult);

        $user = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->companyUserManagementMock->expects($this->once())
            ->method('getCurrentUser')
            ->willReturn($user);

        $this->customProcessorMock->expects($this->once())
            ->method('isAllowed')
            ->with($resource)
            ->willReturn($customProcessorResult);
    }
}
