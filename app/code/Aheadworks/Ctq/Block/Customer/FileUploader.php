<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Customer;

use Magento\Framework\View\Element\Template;

/**
 * Class FileUploader
 * @package Aheadworks\Ctq\Block\Customer
 * @method \Aheadworks\Ctq\ViewModel\Customer\FileUploader getViewModel()
 */
class FileUploader extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Ctq::customer/file_uploader.phtml';

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        $this->jsLayout = $this->getViewModel()->prepareJsLayout($this->jsLayout);

        return parent::getJsLayout();
    }
}
