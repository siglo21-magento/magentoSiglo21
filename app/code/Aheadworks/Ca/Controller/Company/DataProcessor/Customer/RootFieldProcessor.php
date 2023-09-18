<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Company\DataProcessor\Customer;

use Aheadworks\Ca\Controller\Company\DataProcessor\DataProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class RootFieldProcessor
 *
 * @package Aheadworks\Ca\Controller\Company\DataProcessor\Customer
 */
class RootFieldProcessor implements DataProcessorInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param BooleanUtils $booleanUtils
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        BooleanUtils $booleanUtils,
        ArrayManager $arrayManager
    ) {
        $this->booleanUtils = $booleanUtils;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Prepare post data for saving
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $path = $this->arrayManager->findPath('is_root', $data);

        if ($path) {
            $is_root = $this->arrayManager->get($path, $data);
            $is_root = $this->booleanUtils->toBoolean($is_root);
            $data = $this->arrayManager->set($path, $data, $is_root);
        }

        return $data;
    }
}
