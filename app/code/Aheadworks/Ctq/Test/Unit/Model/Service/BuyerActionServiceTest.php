<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\Data\QuoteActionInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsInterface;
use Aheadworks\Ctq\Model\Service\BuyerActionService;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Action\ActionManagement;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;

/**
 * Class BuyerActionServiceTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Service
 */
class BuyerActionServiceTest extends TestCase
{
    /**
     * @var BuyerActionService
     */
    private $model;

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
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->statusRestrictionsPoolMock = $this->createPartialMock(RestrictionsPool::class, ['getRestrictions']);
        $this->actionManagementMock = $this->createPartialMock(ActionManagement::class, ['getActionObjects']);
        $this->quoteRepositoryMock = $this->getMockForAbstractClass(QuoteRepositoryInterface::class);

        $this->model = $objectManager->getObject(
            BuyerActionService::class,
            [
                'statusRestrictionsPool' => $this->statusRestrictionsPoolMock,
                'actionManagement' => $this->actionManagementMock,
                'quoteRepository' => $this->quoteRepositoryMock,
            ]
        );
    }

    /**
     * Test testGetAvailableQuoteActions method
     *
     * @param QuoteInterface|\PHPUnit_Framework_MockObject_MockObject|int $quote
     * @dataProvider getAvailableQuoteActionsDataProvider
     */
    public function testGetAvailableQuoteActions($quote)
    {
        if (!$quote instanceof QuoteInterface) {
            $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
            $this->quoteRepositoryMock
                ->method('get')
                ->with($quote)
                ->willReturn($quoteMock);
        } else {
            $quoteMock = $quote;
        }

        $quoteStatus = Status::DECLINED_BY_BUYER;
        $quoteActionMock = $this->getMockForAbstractClass(QuoteActionInterface::class);
        $expected = [$quoteActionMock];
        $buyerAvailableActions = [];

        $quoteMock
            ->method('getStatus')
            ->willReturn($quoteStatus);

        $restrictionsMock = $this->getMockForAbstractClass(RestrictionsInterface::class);
        $restrictionsMock
            ->method('getBuyerAvailableActions')
            ->willReturn($buyerAvailableActions);

        $this->statusRestrictionsPoolMock
            ->method('getRestrictions')
            ->with($quoteStatus)
            ->willReturn($restrictionsMock);

        $this->actionManagementMock
            ->method('getActionObjects')
            ->with($buyerAvailableActions)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getAvailableQuoteActions($quoteMock));
    }

    /**
     * Data provider for tests
     *
     * @return array
     */
    public function getAvailableQuoteActionsDataProvider()
    {
        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        return [
            [$quoteMock],
            [1]
        ];
    }
}
