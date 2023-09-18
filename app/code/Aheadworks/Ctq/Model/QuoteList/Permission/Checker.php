<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\QuoteList\Permission;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class Checker
 * @package Aheadworks\Ctq\Model\QuoteList\Permission
 */
class Checker
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var BuyerPermissionManagementInterface
     */
    private $buyerPermissionManagement;

    /**
     * @param HttpContext $httpContext
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     */
    public function __construct(
        HttpContext $httpContext,
        BuyerPermissionManagementInterface $buyerPermissionManagement
    ) {
        $this->httpContext = $httpContext;
        $this->buyerPermissionManagement = $buyerPermissionManagement;
    }

    /**
     * Check is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        $customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        return $this->buyerPermissionManagement->isAllowQuoteList($customerGroupId, null);
    }
}
