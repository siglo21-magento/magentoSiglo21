<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\AsyncUpdater\Job;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job
 */
interface ProcessorInterface
{
    /**
     * Execute job
     *
     * @param array $configuration
     * @return bool
     */
    public function process($configuration);
}
