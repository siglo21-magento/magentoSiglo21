<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Quote\Expiration\Finder as ExpiredQuoteFinder;
use Aheadworks\Ctq\Model\Quote\Expiration\Notifier;
use Aheadworks\Ctq\Model\Service\QuoteExpirationService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class QuoteExpirationServiceTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Service
 */
class QuoteExpirationServiceTest extends TestCase
{
    /**
     * @var SellerQuoteManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sellerQuoteServiceMock;

    /**
     * @var QuoteRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepositoryMock;

    /**
     * @var ExpiredQuoteFinder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteFinderMock;

    /**
     * @var Notifier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $notifierMock;

    /**
     * @var QuoteExpirationService|\PHPUnit_Framework_MockObject_MockObject
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

        $this->quoteRepositoryMock = $this->getMockForAbstractClass(QuoteRepositoryInterface::class);
        $this->sellerQuoteServiceMock = $this->getMockForAbstractClass(SellerQuoteManagementInterface::class);
        $this->quoteFinderMock = $this->createMock(ExpiredQuoteFinder::class);
        $this->notifierMock = $this->createMock(Notifier::class);

        $this->service = $objectManager->getObject(
            QuoteExpirationService::class,
            [
                'quoteRepository' => $this->quoteRepositoryMock,
                'sellerQuoteService' => $this->sellerQuoteServiceMock,
                'quoteFinder' => $this->quoteFinderMock,
                'notifier' => $this->notifierMock
            ]
        );
    }

    /**
     * Testing of processExpiredQuotes method
     *
     * @throws \ReflectionException
     */
    public function testProcessExpiredQuotes()
    {
        $quoteId = 1;
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $quoteMockArray = [$quoteMock];

        $quoteMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);
        $this->quoteFinderMock
            ->expects($this->once())
            ->method('findExpiredQuotes')
            ->willReturn($quoteMockArray);
        $this->sellerQuoteServiceMock
            ->expects($this->once())
            ->method('changeStatus')
            ->willReturn($quoteMock);

        $this->service->processExpiredQuotes();
    }

    /**
     * Testing of processExpirationReminder method
     *
     * @throws \ReflectionException
     */
    public function testProcessExpirationReminder()
    {
        $quoteId = 1;
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $quoteMockArray = [$quoteMock];

        $this->notifierMock
            ->expects($this->once())
            ->method('notify')
            ->willReturn(true);
        $this->quoteFinderMock
            ->expects($this->once())
            ->method('findQuotesThatGetExpiredSoon')
            ->willReturn($quoteMockArray);
        $quoteMock
            ->expects($this->once())
            ->method('setReminderStatus')
            ->willReturnSelf();

        $this->service->processExpirationReminder();
    }
}
