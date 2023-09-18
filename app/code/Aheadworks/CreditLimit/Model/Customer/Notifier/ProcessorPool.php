<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Customer\Notifier;

/**
 * Class ProcessorPool
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier
 */
class ProcessorPool
{
    /**
     * @var EmailProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Retrieve customer email processor
     *
     * @param string $type
     * @return EmailProcessorInterface|bool
     */
    public function get($type)
    {
        if (!isset($this->processors[$type])) {
            return false;
        }

        if (!$this->processors[$type] instanceof EmailProcessorInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Email processor does not implement required interface: %s.',
                    EmailProcessorInterface::class
                )
            );
        }

        return $this->processors[$type];
    }
}
