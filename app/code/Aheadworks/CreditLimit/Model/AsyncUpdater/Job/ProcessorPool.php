<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\AsyncUpdater\Job;

/**
 * Class ProcessorPool
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job
 */
class ProcessorPool
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(
        $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Get processor
     *
     * @param string $jobType
     * @return ProcessorInterface
     */
    public function getProcessor($jobType)
    {
        if (isset($this->processors[$jobType])) {
            if (!$this->processors[$jobType] instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Job processor does not implement required interface: %s.',
                        ProcessorInterface::class
                    )
                );
            }
            return $this->processors[$jobType];
        }

        throw new \InvalidArgumentException(
            sprintf('Job processor is not found for job type: %s.', $jobType)
        );
    }
}
