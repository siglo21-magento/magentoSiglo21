<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\AsyncUpdater\Job;

use Aheadworks\CreditLimit\Api\Data\JobInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class DataProcessor
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job
 */
class DataProcessor
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param JsonSerializer $serializer
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        JsonSerializer $serializer
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->serializer = $serializer;
    }

    /**
     * Convert to data array for saving
     *
     * @param JobInterface $jobDataObject
     * @return array
     */
    public function processBeforeSave($jobDataObject)
    {
        $jobData = $this->dataObjectProcessor->buildOutputDataArray(
            $jobDataObject,
            JobInterface::class
        );

        return $jobData;
    }

    /**
     * Prepare data to process after load
     *
     * @param array $jobArrayData
     * @return array
     */
    public function processAfterLoad($jobArrayData)
    {
        $jobArrayData[JobInterface::CONFIGURATION]
            = $this->serializer->unserialize($jobArrayData[JobInterface::CONFIGURATION]);

        return $jobArrayData;
    }
}
