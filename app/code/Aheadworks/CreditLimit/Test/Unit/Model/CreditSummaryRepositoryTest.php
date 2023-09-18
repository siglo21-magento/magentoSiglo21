<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model;

use Aheadworks\CreditLimit\Api\Data\SummarySearchResultsInterface;
use Aheadworks\CreditLimit\Model\CreditSummary;
use Aheadworks\CreditLimit\Model\CreditSummaryRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\SummarySearchResultsInterfaceFactory;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary as CreditSummaryResourceModel;
use Aheadworks\CreditLimit\Model\ResourceModel\Customer\Collection as CreditSummaryCollection;
use Aheadworks\CreditLimit\Model\ResourceModel\Customer\CollectionFactory as CreditSummaryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Unit test for CreditSummaryRepository
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model
 */
class CreditSummaryRepositoryTest extends TestCase
{
    /**
     * @var CreditSummaryRepository
     */
    private $model;

    /**
     * @var CreditSummaryResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var SummaryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $creditSummaryFactoryMock;

    /**
     * @var CreditSummaryCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $creditSummaryCollectionFactoryMock;

    /**
     * @var SummarySearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $creditSummaryData = [
        CreditSummary::SUMMARY_ID => 8,
        CreditSummary::CREDIT_LIMIT => 15,
        CreditSummary::CREDIT_BALANCE => -8,
    ];

    /**
     * Init mocks for tests
     *
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->getMockBuilder(CreditSummaryResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->creditSummaryFactoryMock = $this->getMockBuilder(SummaryInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->creditSummaryCollectionFactoryMock = $this->getMockBuilder(CreditSummaryCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(SummarySearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()->getMock();

        $this->model = $objectManager->getObject(
            CreditSummaryRepository::class,
            [
                'resource' => $this->resourceMock,
                'creditSummaryFactory' => $this->creditSummaryFactoryMock,
                'creditSummaryCollectionFactory' => $this->creditSummaryCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
            ]
        );
    }

    /**
     * Testing of save method
     *
     * @throws LocalizedException
     */
    public function testSave()
    {
        /** @var SummaryInterface|\PHPUnit_Framework_MockObject_MockObject $creditSummaryMock */
        $creditSummaryMock = $this->getMockBuilder(CreditSummary::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $creditSummaryMock->expects($this->once())
            ->method('getSummaryId')
            ->willReturn($this->creditSummaryData[CreditSummary::SUMMARY_ID]);

        $this->assertSame($creditSummaryMock, $this->model->save($creditSummaryMock));
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

        /** @var SummaryInterface|\PHPUnit_Framework_MockObject_MockObject $creditSummaryMock */
        $creditSummaryMock = $this->getMockBuilder(CreditSummary::class)
            ->disableOriginalConstructor()->getMock();
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($creditSummaryMock);
    }

    /**
     * Testing of getByCustomerId method
     *
     * @throws NoSuchEntityException
     */
    public function testGetByCustomerId()
    {
        $customerId = 1;

        $this->resourceMock->expects($this->once())
            ->method('loadByCustomerId')
            ->with($customerId)
            ->willReturn($this->creditSummaryData);

        /** @var SummaryInterface|\PHPUnit_Framework_MockObject_MockObject $creditSummaryMock */
        $creditSummaryMock = $this->getMockBuilder(CreditSummary::class)
            ->disableOriginalConstructor()->getMock();
        $this->creditSummaryFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($creditSummaryMock);
        $this->dataObjectHelperMock->expects($this->exactly(2))
            ->method('populateWithArray')
            ->with($creditSummaryMock, $this->creditSummaryData, SummaryInterface::class)
            ->willReturnSelf();
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($creditSummaryMock, SummaryInterface::class)
            ->willReturn($this->creditSummaryData);

        $this->assertSame($creditSummaryMock, $this->model->getByCustomerId($customerId));
    }

    /**
     * Testing of getByCustomerId method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with customer_id = 1
     */
    public function testGetByCustomerIdOnException()
    {
        $customerId = 1;
        $this->resourceMock->expects($this->once())
            ->method('loadByCustomerId')
            ->with($customerId)
            ->willReturn([]);

        $this->model->getByCustomerId($customerId);
    }

    /**
     * Testing of getList method
     *
     * @throws LocalizedException
     * @throws \ReflectionException
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var CreditSummaryCollection|\PHPUnit_Framework_MockObject_MockObject $creditSummaryCollectionMock */
        $creditSummaryCollectionMock = $this->getMockBuilder(CreditSummaryCollection::class)
            ->disableOriginalConstructor()->getMock();

        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(SummarySearchResultsInterface::class);
        /** @var CreditSummary|\PHPUnit_Framework_MockObject_MockObject $creditSummaryModelMock */
        $creditSummaryModelMock = $this->getMockBuilder(CreditSummary::class)
            ->disableOriginalConstructor()->getMock();
        /** @var SummaryInterface|\PHPUnit_Framework_MockObject_MockObject $creditSummaryMock */
        $creditSummaryMock = $this->getMockForAbstractClass(SummaryInterface::class);

        $this->creditSummaryCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($creditSummaryCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($creditSummaryCollectionMock, SummaryInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $creditSummaryCollectionMock);

        $creditSummaryCollectionMock->expects($this->once())
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

        $creditSummaryCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$creditSummaryModelMock]);

        $this->creditSummaryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($creditSummaryMock);
        $creditSummaryModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->creditSummaryData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($creditSummaryMock, $this->creditSummaryData, SummaryInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$creditSummaryMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
