<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Magento\ModuleUser\UserLoader;

use Magento\Framework\Authorization\PolicyInterface;
use Magento\User\Api\Data\UserInterface;

/**
 * Class AclChecker
 *
 * @package Aheadworks\Ctq\Model\Magento\ModuleUser\UserLoader
 */
class AclChecker
{
    /**
     * @var PolicyInterface
     */
    private $policy;

    /**
     * @param PolicyInterface $policy
     */
    public function __construct(
        PolicyInterface $policy
    ) {
        $this->policy = $policy;
    }

    /**
     * Check if admin user is allowed to view specified resource
     *
     * @param UserInterface $user
     * @param string $resourceId
     * @return bool
     */
    public function isAllowed($user, $resourceId)
    {
        $roleId = $user->getRole()->getRoleId();
        return $this->policy->isAllowed($roleId, $resourceId);
    }
}
