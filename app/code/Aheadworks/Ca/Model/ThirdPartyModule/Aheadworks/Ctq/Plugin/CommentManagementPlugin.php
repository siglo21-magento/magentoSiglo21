<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class CommentManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class CommentManagementPlugin
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
     * Change customer to current company user
     *
     * @param \Aheadworks\Ctq\Api\CommentManagementInterface $subject
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface $comment
     * @return array
     */
    public function beforeAddComment($subject, $comment)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser
            && $this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_view')
        ) {
            $comment->setOwnerId($currentUser->getId());
        }

        return [$comment];
    }
}
