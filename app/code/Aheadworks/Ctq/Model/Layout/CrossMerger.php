<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Layout;

/**
 * Class CrossMerger
 * @package Aheadworks\Ctq\Model\Layout
 */
class CrossMerger
{
    /**
     * @var RecursiveMerger
     */
    private $recursiveMerger;

    /**
     * @param RecursiveMerger $recursiveMerger
     */
    public function __construct(RecursiveMerger $recursiveMerger)
    {
        $this->recursiveMerger = $recursiveMerger;
    }

    /**
     * Fetch components definitions and merge into config
     *
     * @param array $config
     * @param array $sourceConfig
     * @return array
     */
    public function merge(array $config, array $sourceConfig)
    {
        foreach ($config as $code => $configData) {
            $config[$code] = isset($sourceConfig[$code])
                ? $this->recursiveMerger->merge($sourceConfig[$code], $configData)
                : [];
            if (empty($config[$code])) {
                unset($config[$code]);
            }
        }
        return $config;
    }
}
