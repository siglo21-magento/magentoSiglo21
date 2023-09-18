<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsInterface;
use Aheadworks\Ctq\Model\Service\BuyerPermissionService;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class BuyerPermissionServiceTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Service
 */
class BuyerPermissionServiceTest extends TestCase
{
    /**
     * @var BuyerPermissionService
     */
    private $model;

    /**
     * @var RestrictionsPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statusRestrictionsPoolMock;

    /**
     * @var QuoteRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepositoryMock;

    /**
     * @var CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->statusRestrictionsPoolMock = $this->createPartialMock(RestrictionsPool::class, ['getRestrictions']);
        $this->quoteRepositoryMock = $this->getMockForAbstractClass(QuoteRepositoryInterface::class);
        $this->cartRepositoryMock = $this->getMockForAbstractClass(CartRepositoryInterface::class);
        $this->configMock = $this->createPartialMock(Config::class, ['getCustomerGroupsToRequestQuote']);
        $this->customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $this->model = $objectManager->getObject(
            BuyerPermissionService::class,
            [
                'statusRestrictionsPool' => $this->statusRestrictionsPoolMock,
                'quoteRepository' => $this->quoteRepositoryMock,
                'cartRepository' => $this->cartRepositoryMock,
                'config' => $this->configMock,
                'customerRepository' => $this->customerRepositoryMock,
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test canBuyQuote method
     *
     * @param array $nextAvailableStatuses
     * @param bool $expected
     * @dataProvider canBuyQuoteDataProvider
     */
    public function testCanBuyQuote($nextAvailableStatuses, $expected)
    {
        $quoteId = 1;
        $quoteStatus = Status::PENDING_BUYER_REVIEW;

        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $this->quoteRepositoryMock
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);

        $quoteMock
            ->method('getStatus')
            ->willReturn($quoteStatus);

        $restrictionsMock = $this->getMockForAbstractClass(RestrictionsInterface::class);
        $restrictionsMock
            ->method('getNextAvailableStatuses')
            ->willReturn($nextAvailableStatuses);

        $this->statusRestrictionsPoolMock
            ->method('getRestrictions')
            ->with($quoteStatus)
            ->willReturn($restrictionsMock);

        $this->assertEquals($expected, $this->model->canBuyQuote($quoteId));
    }

    /**
     * Data provider for tests
     *
     * @return array
     */
    public function canBuyQuoteDataProvider()
    {
        return [
            [[Status::ORDERED], true],
            [[], false],
        ];
    }

    /**
     * Test canRequestQuote method
     */
    public function testCanRequestQuote()
    {
        $expected = true;
        $cartId = 1;
        $customerId = 1;
        $cartMock = $this->getMockForAbstractClass(
            CartInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getCustomerId', 'getStore']
        );
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);

        $cartMock
            ->method('getCustomerId')
            ->willReturn($customerId);
        $cartMock
            ->method('getCustomer')
            ->willReturn($customerMock);
        $cartMock
            ->method('getStore')
            ->willReturn($storeMock);

        $this->cartRepositoryMock
            ->method('getActive')
            ->with($cartId)
            ->willReturn($cartMock);

        $this->initIsAllowedForCurrentCustomerGroup($customerMock, $storeMock);

        $this->assertEquals($expected, $this->model->canRequestQuote($cartId));
    }

    /**
     * Test isAllowQuotesForCustomer method
     */
    public function isAllowQuotesForCustomer()
    {
        $expected = true;
        $customerId = 1;
        $storeId = 1;
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);

        $this->customerRepositoryMock
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $this->storeManagerMock
            ->method('getStore')
            ->with($storeId)
            ->willReturn($customerMock);

        $this->initIsAllowedForCurrentCustomerGroup($customerMock, $storeMock);

        $this->assertEquals($expected, $this->model->isAllowQuotesForCustomer($customerId, $storeId));
    }

    /**
     * Initialize is allowed for current customer group
     *
     * @param CustomerInterface|\PHPUnit_Framework_MockObject_MockObject $customerMock
     * @param StoreInterface|\PHPUnit_Framework_MockObject_MockObject $storeMock
     */
    private function initIsAllowedForCurrentCustomerGroup($customerMock, $storeMock)
    {
        $websiteId = 1;
        $customerGroupId = 1;

        $customerMock
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $storeMock
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->configMock
            ->method('getCustomerGroupsToRequestQuote')
            ->with($websiteId)
            ->willReturn([$customerGroupId]);
    }
}
