<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Test\Unit\Model;

use Aheadworks\Ctq\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ConfigTest
 *
 * @package Aheadworks\Ctq\Test\Unit\Model
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
     * @return void
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
     * Test isQuoteListEnabled method
     *
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsQuoteListEnabled($value)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_IS_QUOTE_LIST_ENABLED, ScopeInterface::SCOPE_WEBSITE, null)
            ->willReturn($value);

        $this->assertEquals($value, $this->model->isQuoteListEnabled());
    }

    /**
     * Test isAutoAcceptEnabled method
     *
     * @param bool $value
     * @dataProvider boolDataProvider
     */
    public function testIsAutoAcceptEnabled($value)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_IS_AUTO_ACCEPT_ENABLED, ScopeInterface::SCOPE_WEBSITE, null)
            ->willReturn($value);

        $this->assertEquals($value, $this->model->isAutoAcceptEnabled());
    }

    /**
     * Test getAutoAcceptComment method
     */
    public function testGetAutoAcceptComment()
    {
        $expected = 'Sample comment';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_AUTO_ACCEPT_COMMENT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getAutoAcceptComment());
    }

    /**
     * Test getCustomerGroupsToRequestAQuote method
     */
    public function testGetCustomerGroupsToRequestQuote()
    {
        $initial = '1,3';
        $expected = [1,3];

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_CUSTOMER_GROUPS_TO_REQUEST_A_QUOTE)
            ->willReturn($initial);

        $this->assertEquals($expected, $this->model->getCustomerGroupsToRequestQuote());
    }

    /**
     * Test getQuoteExpirationPeriodInDays method
     */
    public function testGetQuoteExpirationPeriodInDays()
    {
        $expected = '3';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_QUOTE_EXPIRATION_PERIOD_IN_DAYS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getQuoteExpirationPeriodInDays());
    }

    /**
     * Test getMaxUploadFileSize method
     */
    public function testGetMaxUploadFileSize()
    {
        $expected = 1048576;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FILE_ATTACHMENTS_MAX_UPLOAD_FILE_SIZE)
            ->willReturn(1);

        $this->assertEquals($expected, $this->model->getMaxUploadFileSize());
    }

    /**
     * Test getAllowFileExtensions method
     */
    public function testGetAllowFileExtensions()
    {
        $initial = 'zip,rar';
        $expected = ['zip', 'rar'];

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FILE_ATTACHMENTS_ALLOW_FILE_EXTENSIONS)
            ->willReturn($initial);

        $this->assertEquals($expected, $this->model->getAllowFileExtensions());
    }

    /**
     * Test getSendEmailReminderInDays method
     */
    public function testGetSendEmailReminderInDays()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SEND_EMAIL_REMINDER_IN_DAYS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getSendEmailReminderInDays());
    }

    /**
     * Test getExpirationReminderTemplate method
     */
    public function testGetExpirationReminderTemplate()
    {
        $expected = 'test';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_EXPIRATION_REMINDER_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getExpirationReminderTemplate());
    }

    /**
     * Test getExpirationReminderTemplate method
     */
    public function testGetQuoteAdminChangeTemplate()
    {
        $expected = 'test';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_QUOTE_ADMIN_CHANGE_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getQuoteAdminChangeTemplate());
    }

    /**
     * @return array
     */
    public function boolDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }
}
