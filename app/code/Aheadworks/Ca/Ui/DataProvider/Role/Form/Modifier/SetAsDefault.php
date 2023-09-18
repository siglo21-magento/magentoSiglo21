<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class SetAsDefault
 * @package Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier
 */
class SetAsDefault implements ModifierInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $isDefault = isset($data[RoleInterface::IS_DEFAULT])
            ? $this->booleanUtils->toBoolean($data[RoleInterface::IS_DEFAULT])
            : false;
        $data['is_default_disabled'] = $isDefault;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
