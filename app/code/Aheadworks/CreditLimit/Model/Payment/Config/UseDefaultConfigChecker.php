<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Payment\Config;

use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class UseDefaultConfigChecker
 *
 * @package Aheadworks\CreditLimit\Model\Payment\Config
 */
class UseDefaultConfigChecker
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * Check if used default is checked for config field
     *
     * @param array $config
     * @param string $section
     * @param string $path
     * @return bool
     */
    public function isUsedByDefault($config, $section, $path)
    {
        $result = false;
        if (isset($config['section']) && $config['section'] == $section) {
            $fieldConfig = $this->arrayManager->get($path, $config);
            $result = $fieldConfig['inherit'] ?? false;
        }

        return (bool)$result;
    }
}
