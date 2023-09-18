<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Test\Unit\Model\Service;

use Aheadworks\CreditLimit\Model\Service\CreditLimitJobService;
use Aheadworks\CreditLimit\Api\Data\JobInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Job as JobResource;
use Aheadworks\CreditLimit\Model\AsyncUpdater\Job\DataProcessor;
use Aheadworks\CreditLimit\Model\AsyncUpdater\Job\ProcessorPool;
use Aheadworks\CreditLimit\Model\Source\Job\Status as JobStatus;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CreditLimit\Model\Source\Job\Type as JobType;
use Aheadworks\CreditLimit\Model\AsyncUpdater\Job\ProcessorInterface;

/**
 * Class CreditLimitJobServiceTest
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Service
 */
class CreditLimitJobServiceTest extends TestCase
{
    /**
     * @var CreditLimitJobService
     */
    private $model;

    /**
     * @var JobResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $jobResourceMock;

    /**
     * @var DataProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProcessorMock;

    /**
     * @var ProcessorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $processorPoolMock;

    /**
     * Init mocks for tests
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->jobResourceMock = $this->getMockBuilder(JobResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProcessorMock = $this->getMockBuilder(DataProcessor::class)
            ->disableOriginalConstructor()
            ->setMethods(['processAfterLoad', 'processBeforeSave'])
            ->getMock();
        $this->processorPoolMock = $this->getMockBuilder(ProcessorPool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProcessor'])
            ->getMock();

        $this->model = $objectManager->getObject(
            CreditLimitJobService::class,
            [
                'jobResource' => $this->jobResourceMock,
                'dataProcessor' =>  $this->dataProcessorMock,
                'processorPool' => $this->processorPoolMock
            ]
        );
    }

    /**
     * Test for addNewJob method
     *
     * @throws \ReflectionException
     */
    public function testAddNewJob()
    {
        $result = true;
        $job = $this->createMock(JobInterface::class);
        $jobDataArray = [
            JobInterface::STATUS => 'ready',
            JobInterface::CONFIGURATION => 'config'
        ];
        $this->dataProcessorMock->expects($this->once())
            ->method('processBeforeSave')
            ->with($job)
            ->willReturn($jobDataArray);
        $this->jobResourceMock->expects($this->once())
            ->method('saveJob')
            ->with($jobDataArray)
            ->willReturn($result);

        $this->assertSame($result, $this->model->addNewJob($job));
    }

    /**
     * Test for runAllJobs method
     *
     * @throws \ReflectionException
     */
    public function testRunAllJobs()
    {
        $result = true;
        $jobDataArray = [
            JobInterface::ID => '1',
            JobInterface::STATUS => 'ready',
            JobInterface::TYPE => JobType::UPDATE_CREDIT_LIMIT,
            JobInterface::CONFIGURATION => 'serialized config'
        ];

        $this->jobResourceMock->expects($this->once())
            ->method('getReadyToRunJobs')
            ->willReturn([$jobDataArray]);
        $this->jobResourceMock->expects($this->once())
            ->method('beginTransaction');
        $this->jobResourceMock->expects($this->once())
            ->method('commit');
        $config = [
            'data1' => 'value1'
        ];
        $jobProcessedDataArray = $jobDataArray;
        $jobProcessedDataArray[JobInterface::CONFIGURATION] = $config;
        $this->dataProcessorMock->expects($this->once())
            ->method('processAfterLoad')
            ->with($jobDataArray)
            ->willReturn($jobProcessedDataArray);

        $processor = $this->createMock(ProcessorInterface::class);
        $this->processorPoolMock->expects($this->once())
            ->method('getProcessor')
            ->with(JobType::UPDATE_CREDIT_LIMIT)
            ->willReturn($processor);
        $processor->expects($this->once())
            ->method('process')
            ->with($config)
            ->willReturn(true);
        $jobDataArray[JobInterface::STATUS] = JobStatus::DONE;
        $this->jobResourceMock->expects($this->once())
            ->method('saveJob')
            ->with($jobDataArray)
            ->willReturn($result);

        $this->assertSame($result, $this->model->runAllJobs());
    }
}
