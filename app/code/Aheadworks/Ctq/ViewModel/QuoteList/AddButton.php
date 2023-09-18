<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\Permission\Checker as PermissionChecker;
use Aheadworks\Ctq\Model\Request\Checker as RequestChecker;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class AddButton
 * @package Aheadworks\Ctq\ViewModel\QuoteList
 */
class AddButton implements ArgumentInterface
{
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;
    
    /**
     * @var RequestChecker
     */
    private $requestChecker;

    /**
     * @param PermissionChecker $permissionChecker
     * @param RequestChecker $requestChecker
     */
    public function __construct(
        PermissionChecker $permissionChecker,
        RequestChecker $requestChecker
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->requestChecker = $requestChecker;
    }

    /**
     * Check is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->permissionChecker->isAllowed();
    }

    /**
     * Check is add action allowed
     *
     * @return bool
     */
    public function isAllowedToAdd()
    {
        return $this->isAllowed()
            && !$this->requestChecker->isConfigureAction();
    }
}
