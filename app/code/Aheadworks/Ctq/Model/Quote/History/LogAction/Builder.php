<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

/**
 * Class Builder
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
class Builder implements BuilderInterface
{
    /**
     * @var BuilderInterface[]
     */
    private $builders = [];

    /**
     * @param BuilderInterface[] $builders
     */
    public function __construct(array $builders)
    {
        $this->builders = $builders;
    }

    /**
     * {@inheritdoc}
     */
    public function build($quote)
    {
        $historyActions = [];
        foreach ($this->builders as $builder) {
            $historyActions = array_merge($historyActions, $builder->build($quote));
        }
        return $historyActions;
    }
}
