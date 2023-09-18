<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model\Transaction;

use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for CreditSummaryManagement
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Transaction
 */
class CreditSummaryManagementTest extends TestCase
{
    /**
     * @var CreditSummaryManagement
     */
    private $model;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var SummaryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $summaryInterfaceFactoryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var SummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $summaryRepositoryMock;

    /**
     * Init mocks for tests
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->summaryInterfaceFactoryMock = $this->createPartialMock(
            SummaryInterfaceFactory::class,
            ['create']
        );
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->summaryRepositoryMock = $this->createMock(SummaryRepositoryInterface::class);

        $this->model = $objectManager->getObject(
            CreditSummaryManagement::class,
            [
                'customerRepository' => $this->customerRepositoryMock,
                'summaryInterfaceFactory' => $this->summaryInterfaceFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'summaryRepository' => $this->summaryRepositoryMock
            ]
        );
    }

    /**
     * Test for getCreditSummary method on common use
     */
    public function testGetCreditSummaryCommonUse()
    {
        $customerId = 10;
        $reload = true;
        $summaryMock = $this->createMock(SummaryInterface::class);
        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $reload)
            ->willReturn($summaryMock);
        $summaryMock->expects($this->once())
            ->method('getCurrency')
            ->willReturn('USD');

        $this->assertSame($summaryMock, $this->model->getCreditSummary($customerId, $reload));
    }

    /**
     * Test for getCreditSummary method on new summary
     */
    public function testGetCreditSummaryNewSummary()
    {
        $customerId = 10;
        $reload = true;

        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $reload)
            ->willThrowException(new NoSuchEntityException());
        $summaryMock = $this->createMock(SummaryInterface::class);
        $this->summaryInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($summaryMock);
        $customerMock = $this->createMock(CustomerInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $websiteId = 1;
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $customerMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $summaryMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $summaryMock->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $summaryMock->expects($this->once())
            ->method('getCurrency')
            ->willReturn('USD');

        $this->assertSame($summaryMock, $this->model->getCreditSummary($customerId, $reload));
    }

    /**
     * Test for getCreditSummary method without currency
     */
    public function testGetCreditSummaryWithoutCurrency()
    {
        $customerId = 10;
        $reload = true;
        $summaryMock = $this->createMock(SummaryInterface::class);
        $this->summaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $reload)
            ->willReturn($summaryMock);
        $summaryMock->expects($this->once())
            ->method('getCurrency')
            ->willReturn(null);

        $customerMock = $this->createMock(CustomerInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);
        $websiteId = 1;
        $websiteMock = $this->createMock(Website::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);
        $customerMock->expects($this->exactly(2))
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $currency = 'USD';
        $websiteMock->expects($this->once())
            ->method('getBaseCurrencyCode')
            ->willReturn($currency);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->willReturn($websiteMock);

        $summaryMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $summaryMock->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $summaryMock->expects($this->once())
            ->method('setCurrency')
            ->willReturn($currency);

        $this->assertSame($summaryMock, $this->model->getCreditSummary($customerId, $reload));
    }

    /**
     * Test for saveCreditSummary method
     */
    public function testSaveCreditSummary()
    {
        $summaryMock = $this->createMock(SummaryInterface::class);
        $this->summaryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($summaryMock)
            ->willReturn($summaryMock);

        $this->assertSame($summaryMock, $this->model->saveCreditSummary($summaryMock));
    }
}
