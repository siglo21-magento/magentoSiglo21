<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model;

use Aheadworks\Ctq\Api\Data\QuoteSearchResultsInterface;
use Aheadworks\Ctq\Model\Quote;
use Aheadworks\Ctq\Model\QuoteRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteSearchResultsInterfaceFactory;
use Aheadworks\Ctq\Model\ResourceModel\Quote as QuoteResourceModel;
use Aheadworks\Ctq\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Ctq\Model\ResourceModel\Quote\Collection as QuoteCollection;

/**
 * Class QuoteRepositoryTest
 * @package Aheadworks\Ctq\Test\Unit\Model
 */
class QuoteRepositoryTest extends TestCase
{
    /**
     * @var QuoteRepository
     */
    private $model;

    /**
     * @var QuoteResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var QuoteInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteInterfaceFactoryMock;

    /**
     * @var QuoteCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteCollectionFactoryMock;

    /**
     * @var QuoteSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
     * @var array
     */
    private $quoteData = [
        'quote_id' => 1,
        'name' => 'test quote'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(
            QuoteResourceModel::class,
            ['save', 'load', 'delete', 'setArgumentsForEntity']
        );
        $this->quoteInterfaceFactoryMock = $this->createPartialMock(QuoteInterfaceFactory::class, ['create']);
        $this->quoteCollectionFactoryMock = $this->createPartialMock(QuoteCollectionFactory::class, ['create']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            QuoteSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);
        $this->model = $objectManager->getObject(
            QuoteRepository::class,
            [
                'resource' => $this->resourceMock,
                'quoteInterfaceFactory' => $this->quoteInterfaceFactoryMock,
                'quoteCollectionFactory' => $this->quoteCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var QuoteInterface|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->createPartialMock(Quote::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->quoteData['quote_id']);

        $this->assertSame($quoteMock, $this->model->save($quoteMock));
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

        /** @var QuoteInterface|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->createPartialMock(Quote::class, ['getQuoteId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($quoteMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $quoteId = 1;

        /** @var QuoteInterface|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->createMock(Quote::class);
        $this->quoteInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($quoteMock, $quoteId)
            ->willReturnSelf();
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);

        $this->assertSame($quoteMock, $this->model->get($quoteId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $quoteId = 20;
        $quoteMock = $this->createMock(Quote::class);
        $this->quoteInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($quoteMock, $quoteId)
            ->willReturn(null);

        $this->model->get($quoteId);
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
        $quoteModelMock = $this->createPartialMock(Quote::class, ['getData']);
        /** @var QuoteInterface|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->getMockForAbstractClass(
            QuoteInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['setOrigData']
        );

        $this->quoteCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($quoteCollectionMock, QuoteInterface::class);
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

        $this->quoteInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteMock);
        $quoteModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->quoteData);
        $quoteMock->expects($this->once())
            ->method('setOrigData');
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($quoteMock, $this->quoteData, QuoteInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$quoteMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $quoteId = '123';

        $quoteMock = $this->createMock(Quote::class);
        $quoteMock->expects($this->any())
            ->method('getId')
            ->willReturn($quoteId);
        $this->quoteInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($quoteMock, $quoteId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($quoteMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($quoteId));
    }

    /**
     * Testing of delete method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $quoteMock = $this->createMock(Quote::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($quoteMock)
            ->willThrowException(new \Exception());
        $this->model->delete($quoteMock);
    }
}
