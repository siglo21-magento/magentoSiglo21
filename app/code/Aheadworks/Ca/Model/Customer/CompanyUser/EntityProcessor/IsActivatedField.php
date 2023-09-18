<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class IsActivatedField
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor
 */
class IsActivatedField implements ProcessorInterface
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
     * Process data after load
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function afterLoad($object)
    {
        $isActivated = $object->getIsActivated();
        if ($isActivated == null) {
            $isActivated = true;
        }
        $object->setIsActivated(
            $this->booleanUtils->toBoolean($isActivated)
        );
        return $object;
    }

    /**
     * Process data before save
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function beforeSave($object)
    {
        $isActivated = $object->getIsActivated();
        if ($isActivated == null) {
            $isActivated = true;
        }
        $object->setIsActivated(
            $this->booleanUtils->toBoolean($isActivated)
        );
        return $object;
    }
}
