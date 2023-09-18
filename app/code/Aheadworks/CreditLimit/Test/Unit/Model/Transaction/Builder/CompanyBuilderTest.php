<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Calculator as BalanceCalculator;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Website\CurrencyList;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class CompanyBuilder
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class CompanyBuilderTest extends TestCase
{
    /**
     * @var CompanyBuilder
     */
    private $model;

    /**
     * @var BalanceCalculator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $balanceCalculatorMock;

    /**
     * @var CurrencyList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $currencyListMock;

    /**
     * @var TransactionActionSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionActionSourceMock;

    /**
     * @var CreditSummaryManagement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $summaryManagementMock;

    /**
     * Init mocks for tests
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->transactionActionSourceMock = $this->createMock(TransactionActionSource::class);
        $this->summaryManagementMock = $this->createMock(CreditSummaryManagement::class);
        $this->balanceCalculatorMock = $this->createMock(BalanceCalculator::class);
        $this->currencyListMock = $this->createMock(CurrencyList::class);

        $this->model = $objectManager->getObject(
            CompanyBuilder::class,
            [
                'transactionActionSource' => $this->transactionActionSourceMock,
                'summaryManagement' => $this->summaryManagementMock,
                'balanceCalculator' => $this->balanceCalculatorMock,
                'currencyList' => $this->currencyListMock
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
            ->method('getCompanyId')
            ->willReturn(12);

        $this->assertSame($result, $this->model->checkIsValid($transactionParamsMock));
    }

    /**
     * Test for build method
     */
    public function testBuild()
    {
        $transactionParamsMock = $this->createMock(TransactionParametersInterface::class);
        $transactionMock = $this->createMock(TransactionInterface::class);

        $customerId = 2;
        $companyId = 1;
        $transactionParamsMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $transactionParamsMock->expects($this->exactly(2))
            ->method('getCompanyId')
            ->willReturn($companyId);
        $summaryMock = $this->createMock(SummaryInterface::class);
        $this->summaryManagementMock->expects($this->once())
            ->method('getCreditSummary')
            ->with($customerId)
            ->willReturn($summaryMock);
        $summaryMock->expects($this->once())
            ->method('getCompanyId')
            ->willReturn(null);
        $summaryMock->expects($this->once())
            ->method('setCompanyId')
            ->with($companyId)
            ->willReturnSelf();
        $this->summaryManagementMock->expects($this->once())
            ->method('saveCreditSummary')
            ->with($summaryMock)
            ->willReturn($summaryMock);
        $transactionMock->expects($this->once())
            ->method('setSummaryId')
            ->with(null)
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('setCompanyId')
            ->with($companyId)
            ->willReturnSelf();

        $this->model->build($transactionMock, $transactionParamsMock);
    }
}
