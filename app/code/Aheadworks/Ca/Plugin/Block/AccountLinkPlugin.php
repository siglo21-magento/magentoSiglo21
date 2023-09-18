<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Block;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Magento\Framework\View\Element\Html\Link\Current;

/**
 * Class AccountLinkPlugin
 * @package Aheadworks\Ca\Plugin\Block
 */
class AccountLinkPlugin
{
    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @param AuthorizationManagementInterface $authorizationManagement
     */
    public function __construct(
        AuthorizationManagementInterface $authorizationManagement
    ) {
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * @param Current $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundToHtml($subject, $proceed)
    {
        $html = '';
        $path = $subject->getPath();
        if ($this->authorizationManagement->isAllowed($path)) {
            $html = $proceed();
        }
        return $html;
    }
}
