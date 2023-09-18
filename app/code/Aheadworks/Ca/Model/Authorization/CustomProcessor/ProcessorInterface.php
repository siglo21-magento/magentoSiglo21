<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\CustomProcessor;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Ca\Model\Authorization\CustomProcessor
 */
interface ProcessorInterface
{
    /**
     * Check current user permission on resource
     *
     * @param string $resource
     * @return bool
     */
    public function isAllowed($resource);
}
