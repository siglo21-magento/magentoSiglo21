<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Status;

/**
 * Class RestrictionsPool
 * @package Aheadworks\Ctq\Model\Quote\Status
 */
class RestrictionsPool
{
    /**
     * @var RestrictionsInterfaceFactory
     */
    private $restrictionsFactory;

    /**
     * @var array
     */
    private $restrictions = [];

    /**
     * @var RestrictionsInterface[]
     */
    private $restrictionsInstance = [];

    /**
     * @param RestrictionsInterfaceFactory $restrictionsFactory
     * @param array $restrictions
     */
    public function __construct(
        RestrictionsInterfaceFactory $restrictionsFactory,
        $restrictions = []
    ) {
        $this->restrictionsFactory = $restrictionsFactory;
        $this->restrictions = $restrictions;
    }

    /**
     * Retrieve restrictions by status
     *
     * @param int $status
     * @return RestrictionsInterface
     * @throws \Exception
     */
    public function getRestrictions($status)
    {
        if (!isset($this->restrictionsInstance[$status])) {
            if (!isset($this->restrictions[$status])) {
                throw new \Exception(sprintf('Unknown status: %s requested', $status));
            }
            $instance = $this->restrictionsFactory->create(['data' => $this->restrictions[$status]]);
            $this->restrictionsInstance[$status] = $instance;
        }
        return $this->restrictionsInstance[$status];
    }
}
