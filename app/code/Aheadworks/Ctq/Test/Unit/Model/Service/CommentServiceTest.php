<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\CommentRepositoryInterface;
use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentSearchResultsInterface;
use Aheadworks\Ctq\Model\Service\CommentService;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentServiceTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Service
 */
class CommentServiceTest extends TestCase
{
    /**
     * @var CommentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var CommentService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $service;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->commentRepositoryMock = $this->getMockForAbstractClass(CommentRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->service = $objectManager->getObject(
            CommentService::class,
            [
                'commentRepository' => $this->commentRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Testing of addComment method
     *
     * @throws \ReflectionException
     */
    public function testAddComment()
    {
        $commentMock = $this->getMockForAbstractClass(CommentInterface::class);

        $this->commentRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($commentMock)
            ->willReturn($commentMock);

        $this->assertEquals($commentMock, $this->service->addComment($commentMock));
    }

    /**
     * Testing of addComment throw exception method
     *
     * @throws \ReflectionException
     */
    public function testAddCommentOnException()
    {
        $commentMock = $this->getMockForAbstractClass(CommentInterface::class);

        $this->commentRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($commentMock)
            ->willThrowException(new LocalizedException(__('some exception')));

        $this->expectException(LocalizedException::class);
        $this->service->addComment($commentMock);
    }

    /**
     * Testing of getAttachment method
     *
     * @throws \ReflectionException
     */
    public function testGetAttachment()
    {
        $fileName = 'file.txt';
        $commentId = 1;
        $quoteId = 1;

        $commentMock = $this->getMockForAbstractClass(CommentInterface::class);
        $attachmentMock = $this->getMockForAbstractClass(CommentAttachmentInterface::class);
        $commentSearchResultsMock = $this->getMockForAbstractClass(CommentSearchResultsInterface::class);
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);

        $this->searchCriteriaBuilderMock
            ->method('addFilter')
            ->withConsecutive(
                [CommentInterface::ID, $commentId],
                [CommentInterface::QUOTE_ID, $quoteId],
                [CommentAttachmentInterface::FILE_NAME, $fileName]
            )->willReturnOnConsecutiveCalls(
                $this->searchCriteriaBuilderMock,
                $this->searchCriteriaBuilderMock,
                $this->searchCriteriaBuilderMock
            );
        $this->searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->commentRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($commentSearchResultsMock);
        $commentSearchResultsMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([$commentMock]);
        $commentMock
            ->expects($this->once())
            ->method('getAttachments')
            ->willReturn([$attachmentMock]);
        $attachmentMock
            ->expects($this->once())
            ->method('getFileName')
            ->willReturn($fileName);

        $this->assertEquals($attachmentMock, $this->service->getAttachment(
            $fileName,
            $commentId,
            $quoteId
        ));
    }

    /**
     * Testing of getAttachment method on exception
     *
     * @throws \ReflectionException
     */
    public function testGetAttachmentOnException()
    {
        $fileName = 'file.txt';
        $commentId = 1;
        $quoteId = 1;

        $commentSearchResultsMock = $this->getMockForAbstractClass(CommentSearchResultsInterface::class);
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);

        $this->searchCriteriaBuilderMock
            ->method('addFilter')
            ->withConsecutive(
                [CommentInterface::ID, $commentId],
                [CommentInterface::QUOTE_ID, $quoteId],
                [CommentAttachmentInterface::FILE_NAME, $fileName]
            )->willReturnOnConsecutiveCalls(
                $this->searchCriteriaBuilderMock,
                $this->searchCriteriaBuilderMock,
                $this->searchCriteriaBuilderMock
            );
        $this->searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->commentRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($commentSearchResultsMock);
        $commentSearchResultsMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->expectException(LocalizedException::class);

        $this->service->getAttachment(
            $fileName,
            $commentId,
            $quoteId
        );
    }
}
