<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface AclManagementInterface
 * @api
 */
interface AclManagementInterface
{
    /**
     * Retrieve root resource id
     *
     * @return string
     */
    public function getRootResourceId();

    /**
     * Retrieve resource keys
     *
     * @return string[]
     */
    public function getResourceKeys();

    /**
     * Retrieve resource structure
     *
     * @return string[]
     */
    public function getResourceStructure();
}
