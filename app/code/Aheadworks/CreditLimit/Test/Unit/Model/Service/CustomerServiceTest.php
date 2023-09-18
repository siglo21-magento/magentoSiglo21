<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model\Service;

use Aheadworks\CreditLimit\Model\Service\CustomerService;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Currency\RateConverter;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for CustomerGroupService
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Service
 */
class CustomerServiceTest extends TestCase
{
    /**
     * @var CustomerService
     */
    private $model;

    /**
     * @var SummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $summaryRepositoryMock;

    /**
     * @var RateConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rateConverterMock;

    /**
     * @var PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrencyMock;

    /**
     * Init mocks for tests
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->summaryRepositoryMock = $this->createMock(SummaryRepositoryInterface::class);
        $this->rateConverterMock = $this->createMock(RateConverter::class);
        $this->priceCurrencyMock = $this->createMock(PriceCurrencyInterface::class);

        $this->model = $objectManager->getObject(
            CustomerService::class,
            [
                'summaryRepository' => $this->summaryRepositoryMock,
                'rateConverter' => $this->rateConverterMock,
                'priceCurrency' => $this->priceCurrencyMock
            ]
        );
    }

    /**
     * Test for isCreditLimitAvailable method when available
     */
    public function testIsCreditLimitAvailable()
    {
        $isAvailable = true;
        $customerId = 3;
        $this->getSummaryMockForCustomer($customerId);

        $this->assertSame($isAvailable, $this->model->isCreditLimitAvailable($customerId));
    }

    /**
     * Test for isCreditLimitAvailable method when not available
     */
    public function testIsCreditLimitNotAvailable()
    {
        $isAvailable = false;
        $customerId = 3;

        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertSame($isAvailable, $this->model->isCreditLimitAvailable($customerId));
    }

    /**
     * Test for isCreditLimitCustom method
     */
    public function testIsCreditLimitCustom()
    {
        $isCustom = true;
        $customerId = 3;
        $summary = $this->getSummaryMockForCustomer($customerId);
        $summary->expects($this->once())
            ->method('getIsCustomCreditLimit')
            ->willReturn($isCustom);

        $this->assertSame($isCustom, $this->model->isCreditLimitCustom($customerId));
    }

    /**
     * Test for getCreditLimitAmount method on common use
     */
    public function testGetCreditLimitAmountCommonCase()
    {
        $customerId = 3;
        $creditLimit = 1000;

        $summary = $this->getSummaryMockForCustomer($customerId);
        $summary->expects($this->exactly(2))
            ->method('getCreditLimit')
            ->willReturn($creditLimit);

        $this->priceCurrencyMock->expects($this->once())
            ->method('round')
            ->with($creditLimit)
            ->willReturn($creditLimit);

        $this->assertSame($creditLimit, $this->model->getCreditLimitAmount($customerId));
    }

    /**
     * Test for getCreditLimitAmount method for non existing customer
     */
    public function testGetCreditLimitAmountNonExistingCustomer()
    {
        $customerId = 3;
        $creditLimit = null;

        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertSame($creditLimit, $this->model->getCreditLimitAmount($customerId));
    }

    /**
     * Test for getCreditLimitAmount method with currency
     */
    public function testGetCreditLimitAmountWithCurrency()
    {
        $customerId = 3;
        $currency = 'USD';
        $creditLimit = 100;

        $summary = $this->getSummaryMockForCustomer($customerId);
        $summary->expects($this->exactly(2))
            ->method('getCreditLimit')
            ->willReturn($creditLimit);
        $summary->expects($this->once())
            ->method('getCurrency')
            ->willReturn($currency);
        $this->rateConverterMock->expects($this->once())
            ->method('convertAmount')
            ->with($creditLimit, $currency)
            ->willReturn($creditLimit);

        $this->assertSame($creditLimit, $this->model->getCreditLimitAmount($customerId, $currency));
    }

    /**
     * Test for getCreditBalanceAmount method on common use
     */
    public function testGetCreditBalanceAmountCommonCase()
    {
        $customerId = 3;
        $creditBalance = 1000;

        $summary = $this->getSummaryMockForCustomer($customerId);
        $summary->expects($this->once())
            ->method('getCreditBalance')
            ->willReturn($creditBalance);
        $this->priceCurrencyMock->expects($this->once())
            ->method('round')
            ->with($creditBalance)
            ->willReturn($creditBalance);

        $this->assertSame($creditBalance, $this->model->getCreditBalanceAmount($customerId));
    }

    /**
     * Test for getCreditBalanceAmount method for non existing customer
     */
    public function testGetCreditBalanceAmountNonExistingCustomer()
    {
        $customerId = 3;
        $creditLimit = 0;

        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertSame($creditLimit, $this->model->getCreditBalanceAmount($customerId));
    }

    /**
     * Test for getCreditBalanceAmount method with currency
     */
    public function testGetCreditBalanceAmountWithCurrency()
    {
        $customerId = 3;
        $currency = 'USD';
        $creditLimit = 100;

        $summary = $this->getSummaryMockForCustomer($customerId);
        $summary->expects($this->once())
            ->method('getCreditBalance')
            ->willReturn($creditLimit);
        $summary->expects($this->once())
            ->method('getCurrency')
            ->willReturn($currency);
        $this->rateConverterMock->expects($this->once())
            ->method('convertAmount')
            ->with($creditLimit, $currency)
            ->willReturn($creditLimit);

        $this->assertSame($creditLimit, $this->model->getCreditBalanceAmount($customerId, $currency));
    }

    /**
     * Test for getCreditAvailableAmount method on common use
     */
    public function testGetCreditAvailableAmountCommonCase()
    {
        $customerId = 3;
        $creditAvailable = 1000;

        $summary = $this->getSummaryMockForCustomer($customerId);
        $summary->expects($this->once())
            ->method('getCreditAvailable')
            ->willReturn($creditAvailable);
        $this->priceCurrencyMock->expects($this->once())
            ->method('round')
            ->with($creditAvailable)
            ->willReturn($creditAvailable);

        $this->assertSame($creditAvailable, $this->model->getCreditAvailableAmount($customerId));
    }

    /**
     * Test for getCreditAvailableAmount method for non existing customer
     */
    public function testGetCreditAvailableAmountNonExistingCustomer()
    {
        $customerId = 3;
        $creditLimit = 0;

        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertSame($creditLimit, $this->model->getCreditAvailableAmount($customerId));
    }

    /**
     * Test for getCreditBalanceAmount method with currency
     */
    public function testGetCreditAvailableAmountWithCurrency()
    {
        $customerId = 3;
        $currency = 'USD';
        $creditLimit = 100;

        $summary = $this->getSummaryMockForCustomer($customerId);
        $summary->expects($this->once())
            ->method('getCreditAvailable')
            ->willReturn($creditLimit);
        $summary->expects($this->once())
            ->method('getCurrency')
            ->willReturn($currency);
        $this->rateConverterMock->expects($this->once())
            ->method('convertAmount')
            ->with($creditLimit, $currency)
            ->willReturn($creditLimit);

        $this->assertSame($creditLimit, $this->model->getCreditAvailableAmount($customerId, $currency));
    }

    /**
     * Get summary mock for customer
     *
     * @param int $customerId
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getSummaryMockForCustomer($customerId)
    {
        $summary = $this->createMock(SummaryInterface::class);
        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willReturn($summary);

        return $summary;
    }
}
