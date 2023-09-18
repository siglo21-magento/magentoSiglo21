<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model;

use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Api\Data\GroupInterfaceFactory;
use Aheadworks\Ca\Api\Data\GroupSearchResultsInterface;
use Aheadworks\Ca\Api\Data\GroupSearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\Group;
use Aheadworks\Ca\Model\GroupRepository;
use Aheadworks\Ca\Model\ResourceModel\Group as GroupResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Aheadworks\Ca\Model\ResourceModel\Group\Collection as GroupCollection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class GroupRepositoryTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model+
 */
class GroupRepositoryTest extends TestCase
{
    /**
     * @var GroupRepository
     */
    private $model;

    /**
     * @var GroupResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceModelMock;

    /**
     * @var GroupInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupInterfaceFactoryMock;

    /**
     * @var GroupCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupCollectionFactoryMock;

    /**
     * @var GroupSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $groupData = [
        'id' => 1,
        'parent_id' => 0,
        'path' => '10/'
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
            GroupResourceModel::class,
            ['save', 'load', 'delete', 'setArgumentsForEntity']
        );
        $this->groupInterfaceFactoryMock = $this->createPartialMock(
            GroupInterfaceFactory::class,
            ['create']
        );
        $this->groupCollectionFactoryMock = $this->createPartialMock(
            GroupCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            GroupSearchResultsInterfaceFactory::class,
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
            GroupRepository::class,
            [
                'resource' => $this->resourceModelMock,
                'groupInterfaceFactory' => $this->groupInterfaceFactoryMock,
                'groupCollectionFactory' => $this->groupCollectionFactoryMock,
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
        $groupId = 1;

        /** @var GroupInterface|\PHPUnit_Framework_MockObject_MockObject $groupMock */
        $groupMock = $this->createMock(Group::class);
        $this->groupInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($groupMock);
        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($groupMock, $groupId)
            ->willReturnSelf();
        $groupMock->expects($this->once())
            ->method('getId')
            ->willReturn($groupId);

        $this->assertSame($groupMock, $this->model->get($groupId));
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var GroupInterface|\PHPUnit_Framework_MockObject_MockObject $groupMock */
        $groupMock = $this->createPartialMock(Group::class, ['getId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $groupMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->groupData['id']);

        $this->assertSame($groupMock, $this->model->save($groupMock));
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

        /** @var GroupInterface|\PHPUnit_Framework_MockObject_MockObject $groupMock */
        $groupMock = $this->createPartialMock(Group::class, ['getId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($groupMock);
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $groupId = 20;
        $groupMock = $this->createMock(Group::class);
        $this->groupInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($groupMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($groupMock, $groupId)
            ->willReturn(null);

        $this->model->get($groupId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var GroupCollection|\PHPUnit_Framework_MockObject_MockObject $groupCollectionMock */
        $groupCollectionMock = $this->createPartialMock(
            GroupCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(GroupSearchResultsInterface::class);
        /** @var Group|\PHPUnit_Framework_MockObject_MockObject $groupModelMock */
        $groupModelMock = $this->createPartialMock(Group::class, ['getData']);
        /** @var GroupInterface|\PHPUnit_Framework_MockObject_MockObject $groupMock */
        $groupMock = $this->getMockForAbstractClass(GroupInterface::class);

        $this->groupCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($groupCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($groupCollectionMock, GroupInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $groupCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $groupCollectionMock->expects($this->once())
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

        $groupCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$groupModelMock]);

        $this->groupInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($groupMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($groupModelMock, GroupInterface::class)
            ->willReturn($this->groupData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($groupMock, $this->groupData, GroupInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$groupMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
