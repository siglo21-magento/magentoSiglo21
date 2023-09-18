<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Action;

/**
 * Class ActionManagement
 * @package Aheadworks\Ctq\Model\Quote\Action
 */
class ActionManagement
{
    /**
     * @var Pool
     */
    private $actionPool;

    /**
     * @param Pool $actionPool
     */
    public function __construct(
        Pool $actionPool
    ) {
        $this->actionPool = $actionPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionObjects($actions)
    {
        $actionObjects = [];
        foreach ($actions as $action) {
            $actionObjects[] = $this->actionPool->getAction($action);
        }
        $actionObjects = $this->sort($actionObjects);

        return $actionObjects;
    }

    /**
     * Sorting modifiers according to sort order
     *
     * @param array $data
     * @return array
     */
    protected function sort(array $data)
    {
        usort($data, function ($a, $b) {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });

        return $data;
    }
}
