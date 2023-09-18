<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api;

/**
 * Interface CreditLimitJobManagementInterface
 * @api
 */
interface CreditLimitJobManagementInterface
{
    /**
     * Add new job
     *
     * @param \Aheadworks\CreditLimit\Api\Data\JobInterface $job
     * @return bool
     * @throws \Exception
     */
    public function addNewJob(\Aheadworks\CreditLimit\Api\Data\JobInterface $job);

    /**
     * Run all ready to process jobs
     *
     * @return bool
     */
    public function runAllJobs();
}
