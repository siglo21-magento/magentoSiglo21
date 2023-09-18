<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Model\PageBuilder\Stage;

use Aheadworks\Ctq\Model\Email\Template\RenderState;
use Magento\PageBuilder\Model\Stage\Preview;

/**
 * Class PreviewPlugin
 * @package Aheadworks\Ctq\Plugin\Model\PageBuilder\Stage
 */
class PreviewPlugin
{
    /**
     * @var RenderState
     */
    private $renderState;

    /**
     * @param RenderState $renderState
     */
    public function __construct(
        RenderState $renderState
    ) {
        $this->renderState = $renderState;
    }

    /**
     * Fix magento bug, return bool value
     * for details see Magento\PageBuilder\Model\Stage\Preview::isPreviewMode()
     * method can returns not boolean value, as result fatal error occurs
     *
     * @param Preview $subject
     * @param \Closure $proceed
     * @return bool
     */
    public function aroundIsPreviewMode(
        $subject,
        \Closure $proceed
    ) {
        if ($this->renderState->isRendering()) {
            return false;
        }

        return $proceed();
    }
}
