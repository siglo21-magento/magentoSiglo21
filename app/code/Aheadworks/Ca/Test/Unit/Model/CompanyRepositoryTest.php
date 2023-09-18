<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Test\Unit\Model;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyInterfaceFactory;
use Aheadworks\Ca\Api\Data\CompanySearchResultsInterface;
use Aheadworks\Ca\Api\Data\CompanySearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\Company;
use Aheadworks\Ca\Model\CompanyRepository;
use Aheadworks\Ca\Model\ResourceModel\Company as CompanyResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Collection as CompanyCollection;
use Aheadworks\Ca\Model\ResourceModel\Company\CollectionFactory as CompanyCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CompanyRepositoryTest
 * @package Aheadworks\Ca\Test\Unit\Model
 */
class CompanyRepositoryTest extends TestCase
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var CompanyResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceModelMock;

    /**
     * @var CompanyInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $companyInterfaceFactoryMock;

    /**
     * @var CompanyCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $companyCollectionFactoryMock;

    /**
     * @var CompanySearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var array
     */
    private $companyData = [
        'id' => 1
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resourceModelMock = $this->createPartialMock(
            CompanyResourceModel::class,
            ['save', 'load', 'delete', 'setArgumentsForEntity']
        );
        $this->companyInterfaceFactoryMock = $this->createPartialMock(
            CompanyInterfaceFactory::class,
            ['create']
        );
        $this->companyCollectionFactoryMock = $this->createPartialMock(
            CompanyCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            CompanySearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->createPartialMock(
            JoinProcessorInterface::class,
            ['process', 'extractExtensionAttributes']
        );
        $this->collectionProcessorMock = $this->getMockForAbstractClass(
            CollectionProcessorInterface::class
        );
        $this->dataObjectHelperMock = $this->createPartialMock(
            DataObjectHelper::class,
            ['populateWithArray']
        );
        $this->dataObjectProcessorMock = $this->createPartialMock(
            DataObjectProcessor::class,
            ['buildOutputDataArray']
        );

        $this->companyRepository = $objectManager->getObject(
            CompanyRepository::class,
            [
                'resource' => $this->resourceModelMock,
                'companyInterfaceFactory' => $this->companyInterfaceFactoryMock,
                'companyCollectionFactory' => $this->companyCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $companyId = 1;

        /** @var CompanyInterface|\PHPUnit_Framework_MockObject_MockObject $companyMock */
        $companyMock = $this->createMock(Company::class);
        $this->companyInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($companyMock);
        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($companyMock, $companyId)
            ->willReturnSelf();
        $companyMock->expects($this->once())
            ->method('getId')
            ->willReturn($companyId);

        $this->assertSame($companyMock, $this->companyRepository->get($companyId));
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var CompanyInterface|\PHPUnit_Framework_MockObject_MockObject $companyMock */
        $companyMock = $this->createPartialMock(Company::class, ['getId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $companyMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->companyData['id']);

        $this->assertSame($companyMock, $this->companyRepository->save($companyMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Exception message.
     */
    public function testSaveOnException()
    {
        $exception = new \Exception('Exception message.');

        /** @var CompanyInterface|\PHPUnit_Framework_MockObject_MockObject $companyMock */
        $companyMock = $this->createPartialMock(Company::class, ['getCompanyId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->companyRepository->save($companyMock);
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $companyId = 20;
        $companyMock = $this->createMock(Company::class);
        $this->companyInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($companyMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($companyMock, $companyId)
            ->willReturn(null);

        $this->companyRepository->get($companyId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var CompanyCollection|\PHPUnit_Framework_MockObject_MockObject $companyCollectionMock */
        $companyCollectionMock = $this->createPartialMock(
            CompanyCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(CompanySearchResultsInterface::class);
        /** @var Company|\PHPUnit_Framework_MockObject_MockObject $companyModelMock */
        $companyModelMock = $this->createPartialMock(Company::class, ['getData']);
        /** @var CompanyInterface|\PHPUnit_Framework_MockObject_MockObject $companyMock */
        $companyMock = $this->getMockForAbstractClass(CompanyInterface::class);

        $this->companyCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($companyCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($companyCollectionMock, CompanyInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $companyCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $companyCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $companyCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$companyModelMock]);

        $this->companyInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($companyMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($companyModelMock, CompanyInterface::class)
            ->willReturn($this->companyData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($companyMock, $this->companyData, CompanyInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$companyMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->companyRepository->getList($searchCriteriaMock));
    }
}
