<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\Data\JobInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Job as JobResource;
use Aheadworks\CreditLimit\Api\CreditLimitJobManagementInterface;
use Aheadworks\CreditLimit\Model\AsyncUpdater\Job\DataProcessor;
use Aheadworks\CreditLimit\Model\AsyncUpdater\Job\ProcessorPool;
use Aheadworks\CreditLimit\Model\Source\Job\Status as JobStatus;

/**
 * Class CreditLimitJobService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CreditLimitJobService implements CreditLimitJobManagementInterface
{
    /**
     * @var JobResource
     */
    private $jobResource;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var ProcessorPool
     */
    private $processorPool;

    /**
     * @param JobResource $jobResource
     * @param DataProcessor $dataProcessor
     * @param ProcessorPool $processorPool
     */
    public function __construct(
        JobResource $jobResource,
        DataProcessor $dataProcessor,
        ProcessorPool $processorPool
    ) {
        $this->jobResource = $jobResource;
        $this->dataProcessor = $dataProcessor;
        $this->processorPool = $processorPool;
    }

    /**
     * @inheritdoc
     */
    public function addNewJob(JobInterface $job)
    {
        $jobDataArray = $this->dataProcessor->processBeforeSave($job);
        return $this->jobResource->saveJob($jobDataArray);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function runAllJobs()
    {
        $jobs = $this->jobResource->getReadyToRunJobs();
        foreach ($jobs as $job) {
            try {
                $this->jobResource->beginTransaction();
                $jobData = $this->dataProcessor->processAfterLoad($job);
                $jobProcessor = $this->processorPool->getProcessor($jobData[JobInterface::TYPE]);
                if ($jobProcessor->process($jobData[JobInterface::CONFIGURATION])) {
                    $job[JobInterface::STATUS] = JobStatus::DONE;
                    $this->jobResource->saveJob($job);
                }
                $this->jobResource->commit();
            } catch (\Exception $e) {
                $this->jobResource->rollBack();
                throw new \LogicException(__($e->getMessage()));
            }
        }

        return true;
    }
}
