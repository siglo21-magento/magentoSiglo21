<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Checkout;

use Magento\Framework\View\Element\Template;

/**
 * Class RequestQuoteLink
 * @package Aheadworks\Ctq\Block\Checkout
 * @method \Aheadworks\Ctq\ViewModel\Checkout\RequestQuoteLink getViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Customer\FileUploader getFileUploaderViewModel()
 */
class RequestQuoteLink extends Template
{
    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        $this->jsLayout = $this->getFileUploaderViewModel()->prepareJsLayout($this->jsLayout);

        return parent::getJsLayout();
    }
}
