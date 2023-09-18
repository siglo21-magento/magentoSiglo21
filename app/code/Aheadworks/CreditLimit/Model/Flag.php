<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model;

use Magento\Framework\Flag as FrameworkFlag;

/**
 * Class Flag
 *
 * @package Aheadworks\CreditLimit\Model
 */
class Flag extends FrameworkFlag
{
    /**
     * Cron flag for last execution time of job runner
     */
    const AW_CL_JOB_RUNNER_LAST_EXEC_TIME = 'aw_credit_limit_job_runner_last_exec_time';

    /**
     * Setter for flag code
     *
     * @param string $code
     * @return $this
     */
    public function setAwClFlag($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
