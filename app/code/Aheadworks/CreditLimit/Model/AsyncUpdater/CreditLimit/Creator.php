<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit;

use Aheadworks\CreditLimit\Api\Data\JobInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\JobInterface;
use Aheadworks\CreditLimit\Model\Source\Job\Type as JobType;
use Aheadworks\CreditLimit\Model\Source\Job\Status as JobStatus;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class Creator
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit
 */
class Creator
{
    /**
     * @var JobInterfaceFactory
     */
    private $jobFactory;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param JobInterfaceFactory $jobFactory
     * @param JsonSerializer $serializer
     */
    public function __construct(
        JobInterfaceFactory $jobFactory,
        JsonSerializer $serializer
    ) {
        $this->jobFactory = $jobFactory;
        $this->serializer = $serializer;
    }

    /**
     * Create new job
     *
     * @param array $data
     * @return JobInterface
     */
    public function createNewJob($data)
    {
        /** @var JobInterface $job */
        $job = $this->jobFactory->create();
        $job->setType(JobType::UPDATE_CREDIT_LIMIT)
            ->setStatus(JobStatus::READY)
            ->setConfiguration($this->serializer->serialize($data));

        return $job;
    }
}
