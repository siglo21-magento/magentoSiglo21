<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\Data\QuoteActionInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote;
use Aheadworks\Ctq\Model\Quote\Action\ActionManagement;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\QuoteRepository;
use Aheadworks\Ctq\Model\Service\SellerActionService;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class SellerActionServiceTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Service
 */
class SellerActionServiceTest extends TestCase
{
    /**
     * @var RestrictionsPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statusRestrictionsPoolMock;

    /**
     * @var ActionManagement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $actionManagementMock;

    /**
     * @var QuoteRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepositoryMock;

    /**
     * @var SellerActionService
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

        $this->statusRestrictionsPoolMock = $this->createMock(RestrictionsPool::class);
        $this->actionManagementMock = $this->createMock(ActionManagement::class);
        $this->quoteRepositoryMock = $this->createMock(QuoteRepository::class);

        $this->service = $objectManager->getObject(
            SellerActionService::class,
            [
                'statusRestrictionsPool' => $this->statusRestrictionsPoolMock,
                'actionManagement' => $this->actionManagementMock,
                'quoteRepository' => $this->quoteRepositoryMock
            ]
        );
    }

    /**
     * Testing of getAvailableQuoteActions method
     *
     * @dataProvider getAvailableQuoteActionsDataProvider
     * @param Quote|int $quote
     * @throws \ReflectionException
     */
    public function testGetAvailableQuoteActions($quote)
    {
        $status = Status::PENDING_BUYER_REVIEW;
        $sellerAvailableActions = [];
        $actionMock = $this->getMockForAbstractClass(QuoteActionInterface::class);
        $quoteMock = $this->createMock(Quote::class);
        $statusRestrictionsMock = $this->getMockForAbstractClass(Quote\Status\RestrictionsInterface::class);

        $expected = [$actionMock];

        if (!is_numeric($quote)) {
            $quote
                ->expects($this->any())
                ->method('getStatus')
                ->willReturn($status);
        }
        $quoteMock
            ->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);
        $this->quoteRepositoryMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($quoteMock);
        $this->statusRestrictionsPoolMock
            ->expects($this->once())
            ->method('getRestrictions')
            ->with($status)
            ->willReturn($statusRestrictionsMock);
        $statusRestrictionsMock
            ->expects($this->once())
            ->method('getSellerAvailableActions')
            ->willReturn($sellerAvailableActions);
        $this->actionManagementMock
            ->expects($this->once())
            ->method('getActionObjects')
            ->with($sellerAvailableActions)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->service->getAvailableQuoteActions($quote));
    }

    /**
     * Data provider for testGetAvailableQuoteActions
     * @return array
     * @throws \ReflectionException
     */
    public function getAvailableQuoteActionsDataProvider()
    {
        return [
            'quote as object' => [$this->createMock(Quote::class)],
            'quote as int' => [1]
        ];
    }
}
