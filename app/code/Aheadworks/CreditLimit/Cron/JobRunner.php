<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Cron;

use Aheadworks\CreditLimit\Api\CreditLimitJobManagementInterface;
use Psr\Log\LoggerInterface;
use Aheadworks\CreditLimit\Model\Flag;

/**
 * Class JobRunner
 *
 * @package Aheadworks\CreditLimit\Cron
 */
class JobRunner
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var CreditLimitJobManagementInterface
     */
    private $jobManagement;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param CreditLimitJobManagementInterface $jobManagement
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        CreditLimitJobManagementInterface $jobManagement
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->jobManagement = $jobManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_CL_JOB_RUNNER_LAST_EXEC_TIME)) {
            try {
                $this->jobManagement->runAllJobs();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_CL_JOB_RUNNER_LAST_EXEC_TIME);
        }
    }
}
