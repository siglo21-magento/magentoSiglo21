<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface AuthorizationManagementInterface
 * @api Aheadworks\Ca\Api
 */
interface AuthorizationManagementInterface
{
    /**
     * Check current user permission by path
     *
     * @param string $path
     * @return boolean
     */
    public function isAllowed($path);

    /**
     * Check current user permission by resource
     *
     * @param string $resource
     * @return boolean
     */
    public function isAllowedByResource($resource);
}
