<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Class SellerPermissionManagementInterface
 * @api
 */
interface SellerPermissionManagementInterface
{
    /**
     * Check if can buy quote or not
     *
     * @param int $quoteId
     * @return bool
     */
    public function canBuyQuote($quoteId);
}
