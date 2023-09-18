<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\Data\RoleInterfaceFactory;
use Aheadworks\Ca\Api\Data\RoleSearchResultsInterface;
use Aheadworks\Ca\Api\Data\RoleSearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\Role;
use Aheadworks\Ca\Model\RoleRepository;
use Aheadworks\Ca\Model\ResourceModel\Role as RoleResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Role\CollectionFactory as RoleCollectionFactory;
use Aheadworks\Ca\Model\ResourceModel\Role\Collection as RoleCollection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class RoleRepositoryTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model
 */
class RoleRepositoryTest extends TestCase
{
    /**
     * @var RoleRepository
     */
    private $model;

    /**
     * @var RoleResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceModelMock;

    /**
     * @var RoleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $roleInterfaceFactoryMock;

    /**
     * @var RoleCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $roleCollectionFactoryMock;

    /**
     * @var RoleSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

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
    private $roleData = [
        'id' => 1,
        'company_id' => 3,
        'name' => 'Test Role'
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

        $this->resourceModelMock = $this->createPartialMock(
            RoleResourceModel::class,
            ['save', 'load', 'delete', 'setArgumentsForEntity']
        );
        $this->roleInterfaceFactoryMock = $this->createPartialMock(
            RoleInterfaceFactory::class,
            ['create']
        );
        $this->roleCollectionFactoryMock = $this->createPartialMock(
            RoleCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            RoleSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->createPartialMock(
            JoinProcessorInterface::class,
            ['process', 'extractExtensionAttributes']
        );
        $this->collectionProcessorMock = $this->getMockForAbstractClass(
            CollectionProcessorInterface::class
        );
        $this->dataObjectHelperMock = $this->createPartialMock(
            DataObjectHelper::class,
            ['populateWithArray']
        );
        $this->dataObjectProcessorMock = $this->createPartialMock(
            DataObjectProcessor::class,
            ['buildOutputDataArray']
        );

        $this->model = $objectManager->getObject(
            RoleRepository::class,
            [
                'resource' => $this->resourceModelMock,
                'roleInterfaceFactory' => $this->roleInterfaceFactoryMock,
                'roleCollectionFactory' => $this->roleCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
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
        $roleId = 1;

        /** @var RoleInterface|\PHPUnit_Framework_MockObject_MockObject $roleMock */
        $roleMock = $this->createMock(Role::class);
        $this->roleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($roleMock);
        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($roleMock, $roleId)
            ->willReturnSelf();
        $roleMock->expects($this->once())
            ->method('getId')
            ->willReturn($roleId);

        $this->assertSame($roleMock, $this->model->get($roleId));
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var RoleInterface|\PHPUnit_Framework_MockObject_MockObject $roleMock */
        $roleMock = $this->createPartialMock(Role::class, ['getId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $roleMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->roleData['id']);

        $this->assertSame($roleMock, $this->model->save($roleMock));
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

        /** @var RoleInterface|\PHPUnit_Framework_MockObject_MockObject $roleMock */
        $roleMock = $this->createPartialMock(Role::class, ['getId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($roleMock);
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $roleId = 20;
        $roleMock = $this->createMock(Role::class);
        $this->roleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($roleMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($roleMock, $roleId)
            ->willReturn(null);

        $this->model->get($roleId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var RoleCollection|\PHPUnit_Framework_MockObject_MockObject $roleCollectionMock */
        $roleCollectionMock = $this->createPartialMock(
            RoleCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(RoleSearchResultsInterface::class);
        /** @var Role|\PHPUnit_Framework_MockObject_MockObject $roleModelMock */
        $roleModelMock = $this->createPartialMock(Role::class, ['getData']);
        /** @var RoleInterface|\PHPUnit_Framework_MockObject_MockObject $roleMock */
        $roleMock = $this->getMockForAbstractClass(RoleInterface::class);

        $this->roleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($roleCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($roleCollectionMock, RoleInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $roleCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $roleCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $roleCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$roleModelMock]);

        $this->roleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($roleMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($roleModelMock, RoleInterface::class)
            ->willReturn($this->roleData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($roleMock, $this->roleData, RoleInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$roleMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
