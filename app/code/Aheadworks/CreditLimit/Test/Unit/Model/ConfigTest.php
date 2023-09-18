<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model;

use Aheadworks\CreditLimit\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for Config
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test getEmailSender method
     */
    public function testGetEmailSender()
    {
        $storeId = 1;
        $expected = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailSender($storeId));
    }

    /**
     * Testing of getSenderName method
     */
    public function testGetSenderName()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_name';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $sender . '/name', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getSenderName($storeId));
    }

    /**
     * Testing of getSenderEmail method
     */
    public function testGetSenderEmail()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_email';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $sender . '/email', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getSenderEmail($storeId));
    }

    /**
     * Test isAllowedToSendEmailOnBalanceUpdate method
     */
    public function testIsAllowedToSendEmailOnBalanceUpdate()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_CAN_SEND_EMAIL_ON_BALANCE_UPDATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isAllowedToSendEmailOnBalanceUpdate());
    }

    /**
     * Test getCreditBalanceUpdatedTemplate method
     */
    public function testGetCreditBalanceUpdatedTemplate()
    {
        $expected = 'some_email_template_id';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_CREDIT_BALANCE_UPDATED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCreditBalanceUpdatedTemplate());
    }
}
