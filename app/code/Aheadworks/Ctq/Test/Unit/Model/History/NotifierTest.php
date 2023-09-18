<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\History;

use Aheadworks\Ctq\Model\Email\Template\RenderState;
use Aheadworks\Ctq\Model\History\Notifier;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Aheadworks\Ctq\Model\Email\Sender;
use Aheadworks\Ctq\Model\History\Notifier\ChangeAdminProcessor;
use Aheadworks\Ctq\Model\History\Notifier\Processor;
use Psr\Log\LoggerInterface;

/**
 * Class NotifierTest
 * @package Aheadworks\Ctq\Test\Unit\Model\History
 */
class NotifierTest extends TestCase
{
    /**
     * @var Notifier
     */
    private $model;

    /**
     * @var Sender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $senderMock;

    /**
     * @var Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $baseEmailProcessorMock;

    /**
     * @var ChangeAdminProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $changeAdminEmailProcessorMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var RenderState|\PHPUnit_Framework_MockObject_MockObject
     */
    private $renderStateMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->senderMock = $this->createMock(Sender::class);
        $this->baseEmailProcessorMock = $this->createMock(Processor::class);
        $this->changeAdminEmailProcessorMock = $this->createMock(ChangeAdminProcessor::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->renderStateMock = $this->createMock(RenderState::class);

        $this->model = $objectManager->getObject(
            Notifier::class,
            [
                'sender' => $this->senderMock,
                'baseEmailProcessor' => $this->baseEmailProcessorMock,
                'changeAdminEmailProcessor' => $this->changeAdminEmailProcessorMock,
                'logger' => $this->loggerMock,
                'renderState' => $this->renderStateMock
            ]
        );
    }

    /**
     * Test for notify method
     */
    public function testNotify()
    {
        $recipientEmail = 'email';
        $historyMock = $this->getMockForAbstractClass(HistoryInterface::class);
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $emailMetadataMock->expects($this->exactly(2))
            ->method('getRecipientEmail')
            ->willReturn($recipientEmail);

        $emailMetadataObjects = [$emailMetadataMock];

        $this->baseEmailProcessorMock->expects($this->once())
            ->method('process')
            ->with($historyMock, $quoteMock)
            ->willReturn($emailMetadataObjects);

        $this->changeAdminEmailProcessorMock->expects($this->once())
            ->method('process')
            ->with($historyMock, $quoteMock)
            ->willReturn($emailMetadataObjects);

        $this->renderStateMock->expects($this->exactly(2))
            ->method('isRendering')
            ->withConsecutive([true], [false])
            ->willReturnOnConsecutiveCalls(true, false);

        $this->senderMock->expects($this->exactly(2))
            ->method('send')
            ->with($emailMetadataMock);

        $this->model->notify($historyMock, $quoteMock);
    }
}
