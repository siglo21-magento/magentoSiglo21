<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model;

use Aheadworks\Ca\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ConfigTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model
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
     * Test getDefaultSalesRepresentative method
     */
    public function testGetDefaultSalesRepresentative()
    {
        $expected = '2';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_DEFAULT_SALES_REPRESENTATIVE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getDefaultSalesRepresentative());
    }

    /**
     * Test getEmailSender method
     */
    public function testGetEmailSender()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailSender());
    }

    /**
     * Test getSenderName method
     */
    public function testGetSenderName()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive([Config::XML_PATH_EMAIL_SENDER], ['trans_email/ident_' . $expected . '/name'])
            ->willReturnOnConsecutiveCalls($expected, $expected);

        $this->assertEquals($expected, $this->model->getSenderName());
    }

    /**
     * Test getSenderEmail method
     */
    public function testGetSenderEmail()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive([Config::XML_PATH_EMAIL_SENDER], ['trans_email/ident_' . $expected . '/email'])
            ->willReturnOnConsecutiveCalls($expected, $expected);

        $this->assertEquals($expected, $this->model->getSenderEmail());
    }

    /**
     * Test getNewCompanyApprovedTemplate method
     */
    public function testGetNewCompanyApprovedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_APPROVED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyApprovedTemplate());
    }

    /**
     * Test getNewCompanyApprovedTemplate method
     */
    public function testGetNewCompanySubmittedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_SUBMITTED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanySubmittedTemplate());
    }

    /**
     * Test getNewCompanyDeclinedTemplate method
     */
    public function testGetNewCompanyDeclinedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_DECLINED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyDeclinedTemplate());
    }

    /**
     * Test getCompanyStatusChangedTemplate method
     */
    public function testGetCompanyStatusChangedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_COMPANY_STATUS_CHANGED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCompanyStatusChangedTemplate());
    }

    /**
     * Test getNewCompanyUserCreatedTemplate method
     */
    public function testGetNewCompanyUserCreatedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_USER_CREATED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyUserCreatedTemplate());
    }
}
