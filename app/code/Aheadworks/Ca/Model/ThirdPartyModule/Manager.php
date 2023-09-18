<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Manager
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule
 */
class Manager
{
    /**
     * Aheadworks Cart To Quote module name
     */
    const AW_CTQ_MODULE_NAME = 'Aheadworks_Ctq';

    /**
     * Aheadworks Store Credit module name
     */
    const AW_STC_MODULE_NAME = 'Aheadworks_StoreCredit';

    /**
     * Aheadworks Reward Points module name
     */
    const AW_RP_MODULE_NAME = 'Aheadworks_RewardPoints';

    /**
     * Aheadworks Payment Restrictions module name
     */
    const AW_PAY_REST_MODULE_NAME = 'Aheadworks_PaymentRestrictions';

    /**
     * Aheadworks Credit Limit module name
     */
    const AW_CREDIT_LIMIT_MODULE_NAME = 'Aheadworks_CreditLimit';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var array
     */
    private $allModules = [
        self::AW_CTQ_MODULE_NAME,
        self::AW_STC_MODULE_NAME,
        self::AW_RP_MODULE_NAME,
        self::AW_PAY_REST_MODULE_NAME,
        self::AW_CREDIT_LIMIT_MODULE_NAME
    ];

    /**
     * @param ModuleListInterface $moduleList
     * @param array $allModules
     */
    public function __construct(
        ModuleListInterface $moduleList,
        $allModules = []
    ) {
        $this->moduleList = $moduleList;
        $this->allModules = array_merge($allModules, $this->allModules);
    }

    /**
     * Check if Aheadworks Cart To Quote module enabled
     *
     * @return bool
     */
    public function isAwCtqModuleEnabled()
    {
        return $this->moduleList->has(self::AW_CTQ_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Store Credit module enabled
     *
     * @return bool
     */
    public function isAwStoreCreditModuleEnabled()
    {
        return $this->moduleList->has(self::AW_STC_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Reward Points module enabled
     *
     * @return bool
     */
    public function isAwRewardPointsModuleEnabled()
    {
        return $this->moduleList->has(self::AW_RP_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Payment Restrictions module enabled
     *
     * @return bool
     */
    public function isAwPayRestModuleEnabled()
    {
        return $this->moduleList->has(self::AW_PAY_REST_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Credit Limit module enabled
     *
     * @return bool
     */
    public function isAwCreditLimitModuleEnabled()
    {
        return $this->moduleList->has(self::AW_CREDIT_LIMIT_MODULE_NAME);
    }

    /**
     * Check if module enabled by name
     *
     * @param string $name
     * @return bool
     */
    public function isModuleEnabledByName($name)
    {
        return $this->moduleList->has($name);
    }

    /**
     * Retrieve all third party modules
     *
     * @return array
     */
    public function getAll()
    {
        return $this->allModules;
    }
}
