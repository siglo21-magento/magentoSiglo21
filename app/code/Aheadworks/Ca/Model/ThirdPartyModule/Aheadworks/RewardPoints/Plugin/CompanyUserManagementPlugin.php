<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\TransactionManagement;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CompanyUserManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin
 */
class CompanyUserManagementPlugin
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Manager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Move all customer balance to company balance
     *
     * @param \Aheadworks\Ca\Api\CompanyUserManagementInterface $subject
     * @param bool $result
     * @param int $userId
     * @return bool
     * @throws LocalizedException
     */
    public function afterAssignUserToCompany(
        $subject,
        $result,
        $userId
    ) {
        if ($result && $this->moduleManager->isAwRewardPointsModuleEnabled()) {
            $this->getTransactionManagement()->moveCustomerBalanceToCompanyBalance($userId);
        }
        return $result;
    }

    /**
     * Get transaction management
     *
     * @return TransactionManagement
     */
    public function getTransactionManagement()
    {
        return $this->objectManager->get(TransactionManagement::class);
    }
}
