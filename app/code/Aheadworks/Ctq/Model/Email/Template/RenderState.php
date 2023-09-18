<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Email\Template;

/**
 * Class RenderState
 *
 * @package Aheadworks\Ctq\Model\Email\Template
 */
class RenderState
{
    /**
     * @var bool
     */
    private $isRendering = false;

    /**
     * Is rendering state flag
     *
     * @param bool|null $isRendering
     * @return bool|null
     */
    public function isRendering($isRendering = null)
    {
        if ($isRendering !== null) {
            $this->isRendering = $isRendering;
        }

        return $this->isRendering;
    }
}
