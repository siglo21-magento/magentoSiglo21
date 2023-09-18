<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Converter;

use Magento\Framework\DataObject;

/**
 * Class DataObjectConverter
 * @package Aheadworks\Ctq\Model\Converter
 */
class DataObjectConverter
{
    /**
     * Convert data object to flat array
     *
     * @param DataObject $object
     * @return array
     */
    public function convertObjectToFlatArray($object)
    {
        $outputData = $object->getData();

        $outputData = $this->excludeComplexValue($outputData);

        return $outputData;
    }

    /**
     * Exclude complex value from array
     *
     * @param array $array
     * @return array
     */
    protected function excludeComplexValue(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_object($value) || is_array($value)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
