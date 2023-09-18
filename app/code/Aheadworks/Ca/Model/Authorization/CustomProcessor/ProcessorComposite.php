<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\CustomProcessor;

/**
 * Class ProcessorComposite
 *
 * @package Aheadworks\Ca\Model\Authorization\CustomProcessor
 */
class ProcessorComposite implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * @inheritdoc
     */
    public function isAllowed($resource)
    {
        $result = true;
        foreach ($this->processors as $processor) {
            $result = $processor->isAllowed($resource);
        }

        return $result;
    }
}
