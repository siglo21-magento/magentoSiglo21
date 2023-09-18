<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Provider;

use Magento\Quote\Model\Quote\Address\Total;

/**
 * Class ArrayRetriever
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Provider
 */
class ArrayRetriever
{
    /**
     * Fetch an element from array and then remove it
     *
     * @param string $key
     * @param array $array
     * @return array|Total
     */
    public function retrieveByKey($key, &$array)
    {
        $value = isset($array[$key]) ? $array[$key] : null;
        unset($array[$key]);
        return $value;
    }
}
