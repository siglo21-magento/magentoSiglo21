<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;

interface ProcessorInterface
{
    /**
     * Process data after load
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function afterLoad($object);

    /**
     * Process data before save
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function beforeSave($object);
}
