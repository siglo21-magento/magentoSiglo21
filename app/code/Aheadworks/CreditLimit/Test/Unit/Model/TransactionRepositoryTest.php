<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model;

use Aheadworks\CreditLimit\Api\Data\TransactionSearchResultsInterface;
use Aheadworks\CreditLimit\Model\Transaction;
use Aheadworks\CreditLimit\Model\TransactionRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\TransactionSearchResultsInterfaceFactory;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResourceModel;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction\Collection as TransactionCollection;

/**
 * Unit test for TransactionRepository
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model
 */
class TransactionRepositoryTest extends TestCase
{
    /**
     * @var TransactionRepository
     */
    private $model;

    /**
     * @var TransactionResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TransactionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionFactoryMock;

    /**
     * @var TransactionCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionCollectionFactoryMock;

    /**
     * @var TransactionSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $transactionData = [
        TransactionInterface::ID => 1,
        TransactionInterface::AMOUNT => 15
    ];

    /**
     * Init mocks for tests
     *
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->getMockBuilder(TransactionResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->transactionFactoryMock = $this->getMockBuilder(TransactionInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->transactionCollectionFactoryMock = $this->getMockBuilder(TransactionCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(TransactionSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();
        $this->model = $objectManager->getObject(
            TransactionRepository::class,
            [
                'resource' => $this->resourceMock,
                'transactionFactory' => $this->transactionFactoryMock,
                'transactionCollectionFactory' => $this->transactionCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->transactionData['id']);

        $this->assertSame($transactionMock, $this->model->save($transactionMock));
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

        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($transactionMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $transactionId = 1;

        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()->getMock();
        $this->transactionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($transactionMock, $transactionId)
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('getId')
            ->willReturn($transactionId);

        $this->assertSame($transactionMock, $this->model->get($transactionId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $transactionId = 20;
        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()->getMock();
        $this->transactionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($transactionMock, $transactionId)
            ->willReturn(null);

        $this->model->get($transactionId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var TransactionCollection|\PHPUnit_Framework_MockObject_MockObject $transactionCollectionMock */
        $transactionCollectionMock = $this->getMockBuilder(TransactionCollection::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TransactionSearchResultsInterface::class);
        /** @var Transaction|\PHPUnit_Framework_MockObject_MockObject $transactionModelMock */
        $transactionModelMock = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()->getMock();
        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);

        $this->transactionCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($transactionCollectionMock, TransactionInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $transactionCollectionMock);

        $transactionCollectionMock->expects($this->once())
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

        $transactionCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$transactionModelMock]);

        $this->transactionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($transactionModelMock, TransactionInterface::class)
            ->willReturn($this->transactionData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($transactionMock, $this->transactionData, TransactionInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$transactionMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
