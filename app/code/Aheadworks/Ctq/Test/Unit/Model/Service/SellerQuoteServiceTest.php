<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ctq\Model\Service\SellerQuoteService;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Copier;
use Aheadworks\Ctq\Model\Quote\Expiration\Calculator as ExpirationCalculator;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\Status as ExpirationReminderStatus;
use Magento\Quote\Model\Quote as MagentoQuote;

/**
 * Class SellerQuoteServiceTest
 *
 * @package Aheadworks\Ctq\Model\Service
 */
class SellerQuoteServiceTest extends TestCase
{
    /**
     * @var SellerQuoteService
     */
    private $service;

    /**
     * @var QuoteManagement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteManagementMock;

    /**
     * @var CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var ExpirationCalculator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $expirationCalculatorMock;

    /**
     * @var Copier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteCopierMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->quoteManagementMock = $this->createMock(QuoteManagement::class);
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->quoteCopierMock = $this->createMock(Copier::class);
        $this->expirationCalculatorMock = $this->createMock(ExpirationCalculator::class);
        $this->service = $objectManager->getObject(
            SellerQuoteService::class,
            [
                'quoteManagement' => $this->quoteManagementMock,
                'cartRepository' => $this->cartRepositoryMock,
                'quoteCopier' => $this->quoteCopierMock,
                'expirationCalculator' => $this->expirationCalculatorMock
            ]
        );
    }

    /**
     * Test changeStatus method
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testChangeStatus()
    {
        $quoteMock = $this->createMock(QuoteInterface::class);
        $status = Status::PENDING_SELLER_REVIEW;
        $quoteId = '11';
        $this->quoteManagementMock->expects($this->once())
            ->method('changeStatus')
            ->with($quoteId, $status, true, true)
            ->willReturn($quoteMock);

        $this->assertSame($quoteMock, $this->service->changeStatus($quoteId, $status));
    }

    /**
     * Test for create quote method
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \ReflectionException
     */
    public function testCreateQuote()
    {
        $cartId = 20;
        $cartMock = $this->createMock(CartInterface::class);
        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($cartId)
            ->willReturn($cartMock);
        $this->quoteManagementMock->expects($this->once())
            ->method('validateCartItemsBeforeBuy')
            ->with($cartMock);
        $customerMock = $this->createMock(CustomerInterface::class);
        $customerId = 4;
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $cartMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customerMock);
        $quoteMock = $this->createPartialMock(Quote::class, ['setCustomerId', 'setIsSeller', 'setStatus']);
        $quoteMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $quoteMock->expects($this->once())
            ->method('setStatus')
            ->with(Status::PENDING_SELLER_REVIEW)
            ->willReturnSelf();
        $quoteMock->expects($this->once())
            ->method('setIsSeller')
            ->with(true)
            ->willReturnSelf();
        $this->quoteManagementMock->expects($this->once())
            ->method('createQuote')
            ->with($cartMock, $quoteMock)
            ->willReturn($quoteMock);

        $this->service->createQuote($cartId, $quoteMock);
    }

    /**
     * Test for update quote method
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \ReflectionException
     */
    public function testUpdateQuote()
    {
        $quoteMock = $this->createPartialMock(Quote::class, ['setIsSeller', 'getCartId']);
        $quoteMock->expects($this->once())
            ->method('setIsSeller')
            ->with(true)
            ->willReturnSelf();
        $cartId = 20;
        $quoteMock->expects($this->once())
            ->method('getCartId')
            ->willReturn($cartId);
        $cartMock = $this->createMock(CartInterface::class);
        $this->quoteManagementMock->expects($this->once())
            ->method('validateCartItemsBeforeBuy')
            ->with($cartMock);
        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($cartId)
            ->willReturn($cartMock);
        $this->quoteManagementMock->expects($this->once())
            ->method('updateQuote')
            ->with($cartMock, $quoteMock, true)
            ->willReturn($quoteMock);

        $this->service->updateQuote($quoteMock);
    }

    /**
     * Test UpdateQuote method if an exception occurs
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Could not update the quote!
     */
    public function testUpdateQuoteException()
    {
        $quoteMock = $this->createPartialMock(Quote::class, ['getCartId']);
        $errorMessage = 'Could not update the quote!';
        $cartId = 20;
        $quoteMock->expects($this->once())
            ->method('getCartId')
            ->willReturn($cartId);

        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($cartId)
            ->willThrowException(new NoSuchEntityException(__($errorMessage)));

        $this->service->updateQuote($quoteMock);
    }

    /**
     * Test getCartByQuote method
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetCartByQuote()
    {
        $quoteMock = $this->createMock(QuoteInterface::class);
        $cartMock = $this->createMock(CartInterface::class);
        $this->quoteManagementMock->expects($this->once())
            ->method('getCartByQuote')
            ->with($quoteMock)
            ->willReturn($cartMock);

        $this->assertEquals($cartMock, $this->service->getCartByQuote($quoteMock));
    }

    /**
     * Test for copyQuote method
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \ReflectionException
     */
    public function testCopyQuote()
    {
        $cartMock = $this->createMock(QuoteCartInterface::class);
        $copiedCartMock = $this->createPartialMock(MagentoQuote::class, ['setAwCtqSellerId', 'setIsActive']);
        $quoteMock = $this->createPartialMock(Quote::class, ['getStoreId', 'getCart', 'getSellerId']);
        $copiedQuoteMock = $this->createPartialMock(
            Quote::class,
            ['setIsSeller', 'setStatus', 'setReminderDate', 'setExpirationDate', 'setReminderStatus']
        );

        $quoteMock->expects($this->once())
            ->method('getCart')
            ->willReturn($cartMock);
        $storeId = 1;
        $quoteMock->expects($this->once())
            ->method('getCart')
            ->willReturn($cartMock);
        $quoteMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->quoteCopierMock->expects($this->once())
            ->method('copyCart')
            ->with($cartMock)
            ->willReturn($copiedCartMock);
        $sellerId = 10;
        $quoteMock->expects($this->once())
            ->method('getSellerId')
            ->willReturn($sellerId);
        $copiedCartMock->expects($this->once())
            ->method('setAwCtqSellerId')
            ->with($sellerId)
            ->willReturnSelf();
        $copiedCartMock->expects($this->once())
            ->method('setIsActive')
            ->with(false)
            ->willReturnSelf();
        $this->cartRepositoryMock->expects($this->once())
            ->method('save')
            ->with($copiedCartMock)
            ->willReturn($copiedCartMock);

        $this->quoteCopierMock->expects($this->once())
            ->method('copyQuote')
            ->with($quoteMock)
            ->willReturn($copiedQuoteMock);

        $copiedQuoteMock->expects($this->once())
            ->method('setStatus')
            ->with(Status::PENDING_SELLER_REVIEW)
            ->willReturnSelf();
        $copiedQuoteMock->expects($this->once())
            ->method('setIsSeller')
            ->with(true)
            ->willReturnSelf();
        $copiedQuoteMock->expects($this->once())
            ->method('setReminderDate')
            ->with(null)
            ->willReturnSelf();
        $expirationDate = '12.12.2020';
        $this->expirationCalculatorMock->expects($this->once())
            ->method('calculateExpirationDate')
            ->with($storeId)
            ->willReturn($expirationDate);
        $copiedQuoteMock->expects($this->once())
            ->method('setExpirationDate')
            ->with($expirationDate)
            ->willReturnSelf();
        $copiedQuoteMock->expects($this->once())
            ->method('setReminderStatus')
            ->with(ExpirationReminderStatus::READY_TO_BE_SENT)
            ->willReturnSelf();

        $this->quoteManagementMock->expects($this->once())
            ->method('createQuote')
            ->with($copiedCartMock, $copiedQuoteMock)
            ->willReturn($copiedQuoteMock);

        $this->service->copyQuote($quoteMock);
    }
}
