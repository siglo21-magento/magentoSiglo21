<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model\Customer\CompanyUser;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyInterfaceFactory;
use Aheadworks\Ca\Api\Data\CompanySearchResultsInterface;
use Aheadworks\Ca\Api\Data\CompanySearchResultsInterfaceFactory;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterfaceFactory;
use Aheadworks\Ca\Model\Company;
use Aheadworks\Ca\Model\CompanyRepository;
use Aheadworks\Ca\Model\Customer\CompanyUser;
use Aheadworks\Ca\Model\Customer\CompanyUser\Repository;
use Aheadworks\Ca\Model\ResourceModel\Company as CompanyResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Collection as CompanyCollection;
use Aheadworks\Ca\Model\ResourceModel\Company\CollectionFactory as CompanyCollectionFactory;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser as CompanyUserResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryTest
 * @package Aheadworks\Ca\Test\Unit\Model\Customer\CompanyUser
 */
class RepositoryTest extends TestCase
{
    /**
     * @var \Aheadworks\Ca\Model\Customer\CompanyUser\Repository
     */
    private $repository;

    /**
     * @var CompanyUserResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var CompanyUserInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $companyUserInterfaceFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var array
     */
    private $companyUserData = [
        'id' => 1
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->createMock(
            CompanyUserResourceModel::class
        );
        $this->companyUserInterfaceFactoryMock = $this->createPartialMock(
            CompanyUserInterfaceFactory::class,
            ['create']
        );
        $this->dataObjectHelperMock = $this->createPartialMock(
            DataObjectHelper::class,
            ['populateWithArray']
        );
        $this->dataObjectProcessorMock = $this->createPartialMock(
            DataObjectProcessor::class,
            ['buildOutputDataArray']
        );

        $this->repository = $objectManager->getObject(
            Repository::class,
            [
                'resource' => $this->resourceMock,
                'companyUserInterfaceFactory' => $this->companyUserInterfaceFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $companyUserId = 1;

        /** @var CompanyUserInterface|\PHPUnit_Framework_MockObject_MockObject $companyUserMock */
        $companyUserMock = $this->createMock(CompanyUser::class);

        $this->companyUserInterfaceFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($companyUserMock);
        $this->resourceMock
            ->expects($this->once())
            ->method('load')
            ->with($companyUserMock, $companyUserId)
            ->willReturnSelf();
        $companyUserMock
            ->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($companyUserId);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->willReturn($this->companyUserData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($companyUserMock, $this->companyUserData, CompanyUserInterface::class);

        $this->assertSame($companyUserMock, $this->repository->get($companyUserId));
    }

    /**
     * Testing of get method
     */
    public function testGetOnException()
    {
        $companyUserId = 2;

        /** @var CompanyUserInterface|\PHPUnit_Framework_MockObject_MockObject $companyUserMock */
        $companyUserMock = $this->createMock(CompanyUser::class);

        $this->companyUserInterfaceFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($companyUserMock);
        $this->resourceMock
            ->expects($this->once())
            ->method('load')
            ->with($companyUserMock, $companyUserId)
            ->willReturnSelf();
        $companyUserMock
            ->expects($this->once())
            ->method('getCustomerId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->repository->get($companyUserId);
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var CompanyInterface|\PHPUnit_Framework_MockObject_MockObject $companyUserMock */
        $companyUserMock = $this->createMock(CompanyUser::class);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $companyUserMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($this->companyUserData['id']);

        $this->assertSame($companyUserMock, $this->repository->save($companyUserMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Exception message.
     */
    public function testSaveOnException()
    {
        $exception = new \Exception('Exception message.');

        /** @var CompanyInterface|\PHPUnit_Framework_MockObject_MockObject $companyMock */
        $companyUserMock = $this->createMock(CompanyUser::class);
        $this->resourceMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->repository->save($companyUserMock);
    }
}
