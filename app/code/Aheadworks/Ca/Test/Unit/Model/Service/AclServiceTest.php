<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model\Service;

use Magento\Framework\Acl\RootResource;
use Magento\Framework\Acl\AclResource\ProviderInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ca\Model\Service\AclService;
use Magento\Framework\Acl;

/**
 * Class AclServiceTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model\Service
 */
class AclServiceTest extends TestCase
{
    /**
     * @var AclService
     */
    private $model;

    /**
     * @var RootResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rootResourceMock;

    /**
     * @var ProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $aclResourceProviderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->rootResourceMock = $this->createMock(RootResource::class);
        $this->aclResourceProviderMock = $this->createMock(ProviderInterface::class);
        $this->model = $objectManager->getObject(
            AclService::class,
            [
                'rootResource' => $this->rootResourceMock,
                'aclResourceProvider' => $this->aclResourceProviderMock
            ]
        );
    }

    /**
     * Test getRootResourceId method
     */
    public function testGetRootResourceId()
    {
        $result = '1';
        $this->rootResourceMock->expects($this->once())
            ->method('getId')
            ->willReturn($result);

        $this->assertEquals($result, $this->model->getRootResourceId());
    }

    /**
     * Test getResourceKeys method
     */
    public function testGetResourceKeys()
    {
        $result = [];
        $this->getResourceStructure($result);

        $this->assertEquals($result, $this->model->getResourceKeys());
    }

    /**
     * Test getResourceStructure method
     */
    public function testGetResourceStructure()
    {
        $result = [];
        $this->getResourceStructure($result);

        $this->assertEquals($result, $this->model->getResourceStructure());
    }

    /**
     * Init resource provider
     *
     * @param array $result
     */
    private function getResourceStructure($result)
    {
        $this->aclResourceProviderMock->expects($this->once())
            ->method('getAclResources')
            ->willReturn($result);
    }
}
