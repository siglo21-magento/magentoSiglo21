<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Company\DataProcessor;

/**
 * Interface DataProcessorInterface
 *
 * @package Aheadworks\Ca\Controller\Company\DataProcessor
 */
interface DataProcessorInterface
{
    /**
     * Prepare post data for saving
     *
     * @param array $data
     * @return array
     */
    public function process($data);
}
