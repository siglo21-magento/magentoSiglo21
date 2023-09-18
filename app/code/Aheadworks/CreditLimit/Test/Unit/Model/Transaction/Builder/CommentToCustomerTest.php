<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface;
use Aheadworks\CreditLimit\Model\Transaction\Builder\CommentToCustomer;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter;
use Aheadworks\CreditLimit\Model\Transaction\Comment\Processor as CommentProcessor;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for CommentToCustomer
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Transaction\Builder
 */
class CommentToCustomerTest extends TestCase
{
    /**
     * @var CommentToCustomer
     */
    private $model;

    /**
     * @var TransactionActionSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionActionSourceMock;

    /**
     * @var CreditSummaryManagement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $summaryManagementMock;

    /**
     * @var EntityConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityConverterMock;

    /**
     * @var CommentProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentProcessorMock;

    /**
     * Init mocks for tests
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->transactionActionSourceMock = $this->createMock(TransactionActionSource::class);
        $this->summaryManagementMock = $this->createMock(CreditSummaryManagement::class);
        $this->entityConverterMock = $this->createMock(EntityConverter::class);
        $this->commentProcessorMock = $this->createMock(CommentProcessor::class);

        $this->model = $objectManager->getObject(
            CommentToCustomer::class,
            [
                'transactionActionSource' => $this->transactionActionSourceMock,
                'summaryManagement' => $this->summaryManagementMock,
                'entityConverter' => $this->entityConverterMock,
                'commentProcessor' => $this->commentProcessorMock
            ]
        );
    }

    /**
     * Test for checkIsValid method when valid
     */
    public function testCheckIsValid()
    {
        $transactionParamsMock = $this->createMock(TransactionParametersInterface::class);
        $result = true;
        $transactionParamsMock->expects($this->once())
            ->method('getCommentToCustomer')
            ->willReturn($result);

        $this->assertSame($result, $this->model->checkIsValid($transactionParamsMock));
    }

    /**
     * Test for checkIsValid method when not valid
     */
    public function testCheckIsNotValid()
    {
        $transactionParamsMock = $this->createMock(TransactionParametersInterface::class);
        $result = false;
        $transactionParamsMock->expects($this->once())
            ->method('getCommentToCustomer')
            ->willReturn($result);
        $transactionParamsMock->expects($this->once())
            ->method('getOrderEntity')
            ->willReturn($result);
        $transactionParamsMock->expects($this->once())
            ->method('getCreditmemoEntity')
            ->willReturn($result);

        $this->assertSame($result, $this->model->checkIsValid($transactionParamsMock));
    }

    /**
     * Test for build method
     */
    public function testBuild()
    {
        $transactionParamsMock = $this->createMock(TransactionParametersInterface::class);
        $transactionMock = $this->createMock(TransactionInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);
        $creditMemoMock = $this->createMock(CreditmemoInterface::class);
        $transactionParamsMock->expects($this->exactly(2))
            ->method('getOrderEntity')
            ->willReturn($orderMock);
        $transactionParamsMock->expects($this->exactly(2))
            ->method('getCreditmemoEntity')
            ->willReturn($creditMemoMock);
        $transactionParamsMock->expects($this->once())
            ->method('getCommentToCustomer')
            ->willReturn(null);

        $transactionEntityMock1 = $this->createMock(TransactionEntityInterface::class);
        $transactionEntityMock2 = $this->createMock(TransactionEntityInterface::class);

        $this->entityConverterMock->expects($this->once())
            ->method('convert')
            ->with([$orderMock, $creditMemoMock])
            ->willReturn([$transactionEntityMock1, $transactionEntityMock2]);
        $transactionParamsMock->expects($this->exactly(2))
            ->method('getAction')
            ->willReturn(TransactionActionSource::CREDIT_BALANCE_UPDATED);
        $placeholder = 'test placeholder';
        $this->commentProcessorMock->expects($this->once())
            ->method('getPlaceholder')
            ->with(TransactionActionSource::CREDIT_BALANCE_UPDATED)
            ->willReturn($placeholder);
        $commentToCustomer = 'comment';
        $this->commentProcessorMock->expects($this->once())
            ->method('renderComment')
            ->with(
                TransactionActionSource::CREDIT_BALANCE_UPDATED,
                [$transactionEntityMock1, $transactionEntityMock2],
                false
            )->willReturn($commentToCustomer);

        $transactionMock->expects($this->once())
            ->method('setCommentToCustomerPlaceholder')
            ->with($placeholder)
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('setEntities')
            ->with([$transactionEntityMock1, $transactionEntityMock2])
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('setCommentToCustomer')
            ->with($commentToCustomer)
            ->willReturnSelf();

        $this->model->build($transactionMock, $transactionParamsMock);
    }
}
