<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterfaceFactory;
use Aheadworks\Ctq\Api\Data\HistorySearchResultsInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\QuoteSearchResultsInterface;
use Aheadworks\Ctq\Model\History;
use Aheadworks\Ctq\Model\HistoryRepository;
use Aheadworks\Ctq\Model\Quote;
use Aheadworks\Ctq\Model\ResourceModel\History as HistoryResourceModel;
use Aheadworks\Ctq\Model\ResourceModel\History\CollectionFactory as HistoryCollectionFactory;
use Aheadworks\Ctq\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class HistoryRepositoryTest
 * @package Aheadworks\Ctq\Test\Unit\Model
 */
class HistoryRepositoryTest extends TestCase
{
    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    /**
     * @var HistoryResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceModelMock;

    /**
     * @var HistoryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $historyInterfaceFactoryMock;

    /**
     * @var HistoryCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $historyCollectionFactoryMock;

    /**
     * @var HistorySearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $historyData = [
        'id' => 1,
        'quote_id' => 1,
        'actions' => []
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
            HistoryResourceModel::class,
            ['save', 'load', 'delete', 'setArgumentsForEntity']
        );
        $this->historyInterfaceFactoryMock = $this->createPartialMock(
            HistoryInterfaceFactory::class,
            ['create']
        );
        $this->historyCollectionFactoryMock = $this->createPartialMock(
            HistoryCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            HistorySearchResultsInterfaceFactory::class,
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

        $this->historyRepository = $objectManager->getObject(
            HistoryRepository::class,
            [
                'resource' => $this->resourceModelMock,
                'historyInterfaceFactory' => $this->historyInterfaceFactoryMock,
                'historyCollectionFactory' => $this->historyCollectionFactoryMock,
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
        /** @var HistoryInterface|\PHPUnit_Framework_MockObject_MockObject $historyMock */
        $historyMock = $this->createPartialMock(History::class, ['getId']);
        $this->historyInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($historyMock);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $historyMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->historyData['id']);

        $this->assertSame($historyMock, $this->historyRepository->save($historyMock));
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

        /** @var HistoryInterface|\PHPUnit_Framework_MockObject_MockObject $historyMock */
        $historyMock = $this->createPartialMock(History::class, ['getQuoteId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->historyRepository->save($historyMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $historyId = 1;

        /** @var HistoryInterface|\PHPUnit_Framework_MockObject_MockObject $historyMock */
        $historyMock = $this->createMock(History::class);
        $this->historyInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($historyMock);
        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($historyMock, $historyId)
            ->willReturnSelf();
        $historyMock->expects($this->once())
            ->method('getId')
            ->willReturn($historyId);

        $this->assertSame($historyMock, $this->historyRepository->get($historyId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $historyId = 20;
        $historyMock = $this->createMock(History::class);
        $this->historyInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($historyMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($historyMock, $historyId)
            ->willReturn(null);

        $this->historyRepository->get($historyId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var QuoteCollection|\PHPUnit_Framework_MockObject_MockObject $quoteCollectionMock */
        $quoteCollectionMock = $this->createPartialMock(
            QuoteCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(QuoteSearchResultsInterface::class);
        /** @var Quote|\PHPUnit_Framework_MockObject_MockObject $quoteModelMock */
        $quoteModelMock = $this->createPartialMock(History::class, ['getData']);
        /** @var QuoteInterface|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->getMockForAbstractClass(HistoryInterface::class);

        $this->historyCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($quoteCollectionMock, HistoryInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $quoteCollectionMock);

        $quoteCollectionMock->expects($this->once())
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

        $quoteCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$quoteModelMock]);

        $this->historyInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($quoteModelMock, HistoryInterface::class)
            ->willReturn($this->historyData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($quoteMock, $this->historyData, HistoryInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$quoteMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->historyRepository->getList($searchCriteriaMock));
    }
}
