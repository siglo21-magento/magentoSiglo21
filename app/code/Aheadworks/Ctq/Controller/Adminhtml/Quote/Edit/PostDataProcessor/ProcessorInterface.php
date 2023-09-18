<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor
 */
interface ProcessorInterface
{
    /**
     * Prepare post data for saving
     *
     * @param array $data
     * @return array
     */
    public function process($data);
}
