<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\Stdlib\BooleanUtils;

class IsRootField implements ProcessorInterface
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
        $isRoot = $object->getIsRoot();
        if ($isRoot == null) {
            $isRoot = false;
        }
        $object->setIsRoot(
            $this->booleanUtils->toBoolean($isRoot)
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
        $isRoot = $object->getIsRoot();
        if ($isRoot == null) {
            $isRoot = false;
        }
        $object->setIsRoot(
            $this->booleanUtils->toBoolean($isRoot)
        );
        return $object;
    }
}
