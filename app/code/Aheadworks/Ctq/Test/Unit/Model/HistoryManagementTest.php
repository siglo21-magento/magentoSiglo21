<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\HistoryRepositoryInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Comment\ToHistory as CommentToHistory;
use Aheadworks\Ctq\Model\History\Notifier;
use Aheadworks\Ctq\Model\HistoryManagement;
use Aheadworks\Ctq\Model\Quote\ToHistory as QuoteToHistory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class HistoryManagementTest
 * @package Aheadworks\Ctq\Test\Unit\Model
 */
class HistoryManagementTest extends TestCase
{
    /**
     * @var HistoryManagement
     */
    private $management;

    /**
     * @var HistoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $historyRepositoryMock;

    /**
     * @var QuoteRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepositoryMock;

    /**
     * @var QuoteToHistory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteToHistoryMock;

    /**
     * @var CommentToHistory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentToHistoryMock;

    /**
     * @var Notifier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $notifierMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->historyRepositoryMock = $this->getMockForAbstractClass(HistoryRepositoryInterface::class);
        $this->quoteRepositoryMock = $this->getMockForAbstractClass(QuoteRepositoryInterface::class);
        $this->quoteToHistoryMock = $this->createMock(QuoteToHistory::class);
        $this->commentToHistoryMock = $this->createMock(CommentToHistory::class);
        $this->notifierMock = $this->createMock(Notifier::class);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->management = $objectManager->getObject(
            HistoryManagement::class,
            [
                'historyRepository' => $this->historyRepositoryMock,
                'quoteRepository' => $this->quoteRepositoryMock,
                'quoteToHistory' => $this->quoteToHistoryMock,
                'commentToHistory' => $this->commentToHistoryMock,
                'notifier' => $this->notifierMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test addQuoteToHistory method
     *
     * @throws \ReflectionException
     */
    public function testAddQuoteToHistory()
    {
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $historyMock = $this->getMockForAbstractClass(HistoryInterface::class);

        $this->quoteToHistoryMock
            ->expects($this->once())
            ->method('convert')
            ->willReturn($historyMock);
        $this->historyRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->willReturn($historyMock);
        $this->notifierMock
            ->expects($this->once())
            ->method('notify');
        $this->management->addQuoteToHistory($quoteMock);
    }

    /**
     * Test testAddQuoteToHistoryOnException method
     *
     * @throws \ReflectionException
     */
    public function testAddQuoteToHistoryOnException()
    {
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $historyMock = $this->getMockForAbstractClass(HistoryInterface::class);
        $exception = new LocalizedException(__('some error'));

        $this->quoteToHistoryMock
            ->expects($this->once())
            ->method('convert')
            ->willReturn($historyMock);
        $this->historyRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->management->addQuoteToHistory($quoteMock);
    }

    /**
     * Test addCommentToHistory method
     *
     * @throws \ReflectionException
     */
    public function testAddCommentToHistory()
    {
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $commentMock = $this->getMockForAbstractClass(CommentInterface::class);
        $historyMock = $this->getMockForAbstractClass(HistoryInterface::class);

        $this->commentToHistoryMock
            ->expects($this->once())
            ->method('convert')
            ->willReturn($historyMock);
        $this->historyRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->willReturn($historyMock);
        $commentMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn(1);
        $this->quoteRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($quoteMock);
        $this->notifierMock
            ->expects($this->once())
            ->method('notify');

        $this->management->addCommentToHistory($commentMock);
    }

    /**
     * Test addCommentToHistoryOnException method
     *
     * @throws \ReflectionException
     */
    public function testAddCommentToHistoryOnException()
    {
        $commentMock = $this->getMockForAbstractClass(CommentInterface::class);
        $historyMock = $this->getMockForAbstractClass(HistoryInterface::class);
        $exception = new NoSuchEntityException(__('some error'));

        $this->commentToHistoryMock
            ->expects($this->once())
            ->method('convert')
            ->willReturn($historyMock);
        $this->historyRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->willReturn($historyMock);
        $commentMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn(1);
        $this->quoteRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $this->management->addCommentToHistory($commentMock);
    }
}
