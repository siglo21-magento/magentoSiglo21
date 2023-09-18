<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Quote\Copier;
use Aheadworks\Ctq\Model\Quote\Expiration\Calculator as ExpirationCalculator;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Aheadworks\Ctq\Model\Service\BuyerQuoteService;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartExtensionInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;

/**
 * Class BuyerQuoteServiceTest
 *
 * @package Aheadworks\Ctq\Test\Unit\Model\Service
 */
class BuyerQuoteServiceTest extends TestCase
{
    /**
     * @var QuoteInterfaceFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteFactoryMock;

    /**
     * @var CartRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var QuoteManagement|PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteManagementMock;

    /**
     * @var BuyerPermissionManagementInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $buyerPermissionManagementMock;

    /**
     * @var ExpirationCalculator|PHPUnit_Framework_MockObject_MockObject
     */
    private $expirationCalculatorMock;

    /**
     * @var Config|PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Copier|PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteCopierMock;

    /**
     * @var CommentInterfaceFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $commentDataFactoryMock;

    /**
     * @var BuyerQuoteService
     */
    private $service;

    /**
     * Init mocks for tests
     *
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->quoteFactoryMock = $this->createPartialMock(
            QuoteInterfaceFactory::class,
            ['create']
        );
        $this->cartRepositoryMock = $this->getMockForAbstractClass(
            CartRepositoryInterface::class
        );
        $this->quoteManagementMock = $this->createMock(QuoteManagement::class);
        $this->buyerPermissionManagementMock = $this->getMockForAbstractClass(
            BuyerPermissionManagementInterface::class
        );
        $this->expirationCalculatorMock = $this->createMock(
            ExpirationCalculator::class
        );
        $this->configMock = $this->createMock(
            Config::class
        );
        $this->quoteCopierMock = $this->createMock(
            Copier::class
        );
        $this->commentDataFactoryMock = $this->createMock(CommentInterfaceFactory::class);

        $this->service = $objectManager->getObject(
            BuyerQuoteService::class,
            [
                'quoteFactory' => $this->quoteFactoryMock,
                'cartRepository' => $this->cartRepositoryMock,
                'quoteManagement' => $this->quoteManagementMock,
                'buyerPermissionManagement' => $this->buyerPermissionManagementMock,
                'expirationCalculator' => $this->expirationCalculatorMock,
                'config' => $this->configMock,
                'quoteCopier' => $this->quoteCopierMock,
                'commentDataFactory' => $this->commentDataFactoryMock
            ]
        );
    }

    /**
     * Testing of requestQuote method
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testRequestQuote()
    {
        $cartId = 1;
        $customerId = 1;
        $sellerId = 1;
        $storeId = 1;
        $quoteName = 'quote';
        $comment = null;
        $websiteId = 1;
        $acceptComment = 'Sample comment';
        $expirationDate = new \DateTime('today', new \DateTimeZone('UTC'));
        $cartMock = $this->createMock(Quote::class);
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $cartExtensionsAttributesMock = $this->getMockForAbstractClass(CartExtensionInterface::class);
        $storeMock = $this->createConfiguredMock(StoreInterface::class, ['getWebsiteId' => $websiteId]);
        $commentMock = $this->createMock(CommentInterface::class);

        $this->buyerPermissionManagementMock
            ->expects($this->once())
            ->method('canRequestQuote')
            ->willReturn(true);
        $this->quoteFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($quoteMock);
        $this->cartRepositoryMock
            ->expects($this->once())
            ->method('getActive')
            ->willReturn($cartMock);
        $cartMock
            ->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($cartExtensionsAttributesMock);
        $cartExtensionsAttributesMock
            ->expects($this->once())
            ->method('setAwCtqQuote')
            ->willReturnSelf();
        $cartMock
            ->expects($this->any())
            ->method('__call')
            ->withConsecutive(
                ['getCustomerId', []],
                ['setAwCtqIsNotRequireValidation', [true]]
            )->willReturnOnConsecutiveCalls(
                [$customerId],
                $cartMock
            );
        $cartMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->expirationCalculatorMock
            ->expects($this->once())
            ->method('calculateExpirationDate')
            ->willReturn($expirationDate);
        $this->configMock
            ->expects($this->once())
            ->method('getQuoteAssignedAdminUser')
            ->willReturn($sellerId);
        $this->quoteManagementMock
            ->expects($this->once())
            ->method('createQuote')
            ->willReturn($quoteMock);
        $quoteMock
            ->expects($this->once())
            ->method('setCustomerId')
            ->willReturnSelf();
        $quoteMock
            ->expects($this->once())
            ->method('setName')
            ->willReturnSelf();
        $quoteMock
            ->expects($this->atLeastOnce())
            ->method('setStatus')
            ->willReturnSelf();
        $quoteMock
            ->expects($this->once())
            ->method('setExpirationDate')
            ->willReturnSelf();
        $quoteMock
            ->expects($this->once())
            ->method('setSellerId')
            ->willReturnSelf();
        $quoteMock
            ->expects($this->atLeastOnce())
            ->method('setComment')
            ->willReturnSelf();
        $cartMock
            ->expects($this->once())
            ->method('setData')
            ->willReturnSelf();
        $this->configMock->expects($this->once())
            ->method('isAutoAcceptEnabled')
            ->with($websiteId)
            ->willReturn(true);
        $cartMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $quoteMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->commentDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($commentMock);
        $commentMock->expects($this->once())
            ->method('setComment')
            ->with($acceptComment);
        $this->quoteManagementMock->expects($this->once())
            ->method('updateQuote')
            ->with($cartMock, $quoteMock, true)
            ->willReturn($quoteMock);
        $this->configMock->expects($this->once())
            ->method('getAutoAcceptComment')
            ->with($storeId)
            ->willReturn($acceptComment);

        $this->assertEquals($quoteMock, $this->service->requestQuote(
            $cartId,
            $quoteName,
            $comment
        ));
    }

    /**
     * Testing of getCartByQuote method
     */
    public function testGetCartByQuote()
    {
        $storeId = 1;
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);

        $this->quoteManagementMock
            ->expects($this->once())
            ->method('getCartByQuote')
            ->willReturn($quoteMock);

        $this->assertEquals($quoteMock, $this->service->getCartByQuote(
            $quoteMock,
            $storeId
        ));
    }

    /**
     * Testing of changeStatus method
     */
    public function testChangeStatus()
    {
        $quoteId = 1;
        $status = Status::PENDING_BUYER_REVIEW;

        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);

        $this->quoteManagementMock
            ->expects($this->once())
            ->method('changeStatus')
            ->willReturn($quoteMock);

        $this->assertEquals($quoteMock, $this->service->changeStatus(
            $quoteId,
            $status
        ));
    }

    /**
     * Testing of updateQuote method
     */
    public function testUpdateQuote()
    {
        $quoteMock = $this->createMock(QuoteInterface::class);
        $cartMock = $this->createMock(Quote::class);
        $websiteId = 1;
        $storeMock = $this->createConfiguredMock(StoreInterface::class, ['getWebsiteId' => $websiteId]);

        $quoteMock
            ->expects($this->once())
            ->method('getCartId')
            ->willReturn(1);
        $cartMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $this->buyerPermissionManagementMock->expects($this->once())
            ->method('isAllowQuoteUpdate')
            ->with($websiteId)
            ->willReturn(true);
        $this->cartRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($cartMock);
        $this->quoteManagementMock
            ->expects($this->once())
            ->method('updateQuote')
            ->willReturn($quoteMock);

        $this->assertEquals($quoteMock, $this->service->updateQuote($quoteMock));
    }
}
