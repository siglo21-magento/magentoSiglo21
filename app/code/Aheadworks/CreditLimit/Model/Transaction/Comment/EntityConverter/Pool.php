<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter;

/**
 * Class Pool
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter
 */
class Pool
{
    /**
     * @var ConverterInterface[]
     */
    private $converters;

    /**
     * @param array $converters
     */
    public function __construct(
        $converters = []
    ) {
        $this->converters = $converters;
    }

    /**
     * Get converter by object type
     *
     * @param string $objectType
     * @return ConverterInterface|null
     */
    public function getConverter($objectType)
    {
        if (isset($this->converters[$objectType])) {
            return $this->converters[$objectType];
        }

        return null;
    }
}
