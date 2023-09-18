<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Email;

/**
 * Interface VariableProcessorInterface
 *
 * @package Aheadworks\CreditLimit\Model\Email
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
