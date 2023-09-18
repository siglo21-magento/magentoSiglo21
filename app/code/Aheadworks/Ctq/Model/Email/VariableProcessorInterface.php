<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Email;

/**
 * Interface VariableProcessorInterface
 * @package Aheadworks\Ctq\Model\Email\VariableProcessor
 */
interface VariableProcessorInterface
{
    /**
     * Prepare variables before send
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables);
}
