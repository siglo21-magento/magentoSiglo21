<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class BuyerPermissionManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class BuyerPermissionManagementPlugin
{
    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        AuthorizationManagementInterface $authorizationManagement,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->authorizationManagement = $authorizationManagement;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Check is customer allowed to quote
     *
     * @param \Aheadworks\Ctq\Api\BuyerPermissionManagementInterface $subject
     * @param callable $proceed
     * @param array $args
     * @return bool
     */
    public function aroundCanRequestQuote($subject, callable $proceed, ...$args)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser) {
            return $this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_allow_using');
        } else {
            return $proceed(...$args);
        }
    }
}
