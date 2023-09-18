<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class Updater
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company
 */
class Updater
{
    /**
     * Request param with credit limit data
     */
    const CREDIT_LIMIT_PARAM = 'aw_credit_limit';

    /**
     * @var Manager
     */
    private $thirdPartyModuleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param Manager $thirdPartyModuleManager
     * @param ObjectManagerInterface $objectManager
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        Manager $thirdPartyModuleManager,
        ObjectManagerInterface $objectManager,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
        $this->companyUserManagement = $companyUserManagement;
        $this->objectManager = $objectManager;
    }

    /**
     * Customer credit limit update after save
     *
     * @param int $customerId
     * @param array $creditLimitData
     * @return bool
     * @throws LocalizedException
     */
    public function updateCreditLimit($customerId, $creditLimitData)
    {
        if (!$this->isAllowedToUpdate($customerId, $creditLimitData)) {
            return false;
        }

        $balanceUpdater = $this->objectManager->get(
            \Aheadworks\CreditLimit\Model\Customer\Backend\BalanceUpdater::class
        );
        $rootUser = $this->companyUserManagement->getRootUserForCompany($customerId);
        $balanceUpdater->updateCreditLimit($rootUser->getId(), $creditLimitData, []);
        $balanceUpdater->updateCreditBalance($rootUser->getId(), $creditLimitData);

        return true;
    }

    /**
     * Is allowed to update credit limit balance
     *
     * @param int $customerId
     * @param array $creditLimitData
     * @return bool
     */
    private function isAllowedToUpdate($customerId, $creditLimitData)
    {
        if (!$customerId || empty($creditLimitData)) {
            return false;
        }

        if (!$this->thirdPartyModuleManager->isAwCreditLimitModuleEnabled()) {
            return false;
        }

        return true;
    }
}
