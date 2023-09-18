<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model\Service;

use Aheadworks\CreditLimit\Model\Service\CustomerGroupService;
use Aheadworks\CreditLimit\Model\ResourceModel\CustomerGroupConfig;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for CustomerGroupService
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Service
 */
class CustomerGroupServiceTest extends TestCase
{
    /**
     * @var CustomerGroupService
     */
    private $model;

    /**
     * @var CustomerGroupConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerGroupConfigResourceMock;

    /**
     * Init mocks for tests
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->customerGroupConfigResourceMock = $this->getMockBuilder(CustomerGroupConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadConfigValue', 'loadData'])
            ->getMock();

        $this->model = $objectManager->getObject(
            CustomerGroupService::class,
            [
                'customerGroupConfigResource' => $this->customerGroupConfigResourceMock
            ]
        );
    }

    /**
     * Test for getCreditLimit method
     */
    public function testGetCreditLimit()
    {
        $groupId = 3;
        $websiteId = 1;
        $creditLimitValue = 20;

        $customerGroupConfig = [
            'customer_group_id' => $groupId,
            SummaryInterface::CREDIT_LIMIT => $creditLimitValue
        ];

        $this->customerGroupConfigResourceMock->expects($this->once())
            ->method('loadConfigValue')
            ->with($websiteId)
            ->willReturn([$customerGroupConfig]);

        $this->assertSame($creditLimitValue, $this->model->getCreditLimit($groupId, $websiteId));
    }

    /**
     * Test for getCreditLimitValuesForWebsite method
     */
    public function testGetCreditLimitValuesForWebsite()
    {
        $groupId = 3;
        $websiteId = 1;
        $creditLimitValue = 20;

        $customerGroupConfig = [
            'customer_group_id' => $groupId,
            SummaryInterface::CREDIT_LIMIT => $creditLimitValue
        ];

        $result = [
            $groupId => $customerGroupConfig[SummaryInterface::CREDIT_LIMIT]
        ];

        $this->customerGroupConfigResourceMock->expects($this->once())
            ->method('loadData')
            ->with($websiteId)
            ->willReturn([$customerGroupConfig]);

        $this->assertSame($result, $this->model->getCreditLimitValuesForWebsite($websiteId));
    }
}
