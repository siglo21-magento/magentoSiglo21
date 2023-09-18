<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Layout;

/**
 * Class RecursiveMerger
 * @package Aheadworks\Ctq\Model\Layout
 */
class RecursiveMerger
{
    /**
     * Perform recursive config merging
     *
     * @param array $target
     * @param array $source
     * @return array
     */
    public function merge(array $target, array $source)
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                if (!isset($target[$key])) {
                    $target[$key] = [];
                }
                $target[$key] = $this->merge($target[$key], $value);
            } else {
                $target[$key] = $value;
            }
        }
        return $target;
    }
}
