<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Ca\ViewModel\Role\Role as RoleViewModel;

/**
 * Class AwRewardPoints
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier
 */
class AwRewardPoints implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RoleViewModel
     */
    private $roleViewModel;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param ArrayManager $arrayManager
     * @param RoleViewModel $roleViewModel
     * @param Manager $moduleManager
     */
    public function __construct(
        ArrayManager $arrayManager,
        RoleViewModel $roleViewModel,
        Manager $moduleManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->roleViewModel = $roleViewModel;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        $baseAmountLimit = isset($data[RoleInterface::AW_RP_BASE_AMOUNT_LIMIT])
            ? $data[RoleInterface::AW_RP_BASE_AMOUNT_LIMIT]
            : 0;

        $data[RoleInterface::AW_RP_BASE_AMOUNT_LIMIT] = $this->roleViewModel->getRoundAmount($baseAmountLimit);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $awRpFieldsetPath = $this->arrayManager->findPath('aw_rp_fieldset', $meta);
        if ($awRpFieldsetPath) {
            $awRpFieldsetConfig['visible'] = $this->moduleManager->isAwRewardPointsModuleEnabled();
            $meta = $this->arrayManager->merge($awRpFieldsetPath, $meta, $awRpFieldsetConfig);
        }

        return $meta;
    }
}
