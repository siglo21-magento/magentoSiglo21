<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Api\Data\CommentSearchResultsInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\QuoteSearchResultsInterface;
use Aheadworks\Ctq\Model\Comment;
use Aheadworks\Ctq\Model\CommentRepository;
use Aheadworks\Ctq\Model\Quote;
use Aheadworks\Ctq\Model\ResourceModel\Comment as CommentResourceModel;
use Aheadworks\Ctq\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;
use Aheadworks\Ctq\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentRepositoryTest
 * @package Aheadworks\Ctq\Test\Unit\Model
 */
class CommentRepositoryTest extends TestCase
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var CommentResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceModelMock;

    /**
     * @var CommentInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentInterfaceFactoryMock;

    /**
     * @var CommentCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentCollectionFactoryMock;

    /**
     * @var CommentSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $commentData = [
        'id' => 1,
        'quote_id' => 1,
        'comment' => 'test comment'
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
            CommentResourceModel::class,
            ['save', 'load', 'delete', 'setArgumentsForEntity']
        );
        $this->commentInterfaceFactoryMock = $this->createPartialMock(
            CommentInterfaceFactory::class,
            ['create']
        );
        $this->commentCollectionFactoryMock = $this->createPartialMock(
            CommentCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            CommentSearchResultsInterfaceFactory::class,
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

        $this->commentRepository = $objectManager->getObject(
            CommentRepository::class,
            [
                'resource' => $this->resourceModelMock,
                'commentInterfaceFactory' => $this->commentInterfaceFactoryMock,
                'commentCollectionFactory' => $this->commentCollectionFactoryMock,
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
        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(Comment::class, ['getId']);
        $this->commentInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($commentMock);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $commentMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->commentData['id']);

        $this->assertSame($commentMock, $this->commentRepository->save($commentMock));
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

        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(Comment::class, ['getQuoteId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->commentRepository->save($commentMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $commentId = 1;

        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createMock(Comment::class);
        $this->commentInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($commentMock);
        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($commentMock, $commentId)
            ->willReturnSelf();
        $commentMock->expects($this->once())
            ->method('getId')
            ->willReturn($commentId);

        $this->assertSame($commentMock, $this->commentRepository->get($commentId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $commentId = 20;
        $commentMock = $this->createMock(Comment::class);
        $this->commentInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($commentMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($commentMock, $commentId)
            ->willReturn(null);

        $this->commentRepository->get($commentId);
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
        $quoteModelMock = $this->createPartialMock(Comment::class, ['getData']);
        /** @var QuoteInterface|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->getMockForAbstractClass(CommentInterface::class);

        $this->commentCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($quoteCollectionMock, CommentInterface::class);
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

        $this->commentInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($quoteModelMock, CommentInterface::class)
            ->willReturn($this->commentData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($quoteMock, $this->commentData, CommentInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$quoteMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->commentRepository->getList($searchCriteriaMock));
    }
}
