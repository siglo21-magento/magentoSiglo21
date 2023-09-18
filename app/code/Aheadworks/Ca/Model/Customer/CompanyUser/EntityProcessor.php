<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor\ProcessorInterface;

/**
 * Class EntityProcessor
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser
 */
class EntityProcessor
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity before save
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function prepareDataBeforeSave($object)
    {
        foreach ($this->processors as $processor) {
            $processor->beforeSave($object);
        }
        return $object;
    }

    /**
     * Prepare entity after load
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function prepareDataAfterLoad($object)
    {
        foreach ($this->processors as $processor) {
            $processor->afterLoad($object);
        }
        return $object;
    }
}
