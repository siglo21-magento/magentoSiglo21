<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Email;

/**
 * Class VariableProcessorComposite
 *
 * @package Aheadworks\CreditLimit\Model\Email
 */
class VariableProcessorComposite implements VariableProcessorInterface
{
    /**
     * @var VariableProcessorInterface[]
     */
    private $processors;

    /**
     * @param VariableProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare variables
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof VariableProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Variable processor does not implement required interface: %s.',
                        VariableProcessorInterface::class
                    )
                );
            }
            $variables = $processor->prepareVariables($variables);
        }
        return $variables;
    }
}
