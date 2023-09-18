<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Shipping;

use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\AbstractEdit;
use Magento\Framework\Phrase;

/**
 * Class Method
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Shipping
 */
class Method extends AbstractEdit
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_shipping_method');
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Shipping Method');
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-shipping-method';
    }
}
