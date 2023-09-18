<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

/**
 * Class QuoteInformation
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class QuoteInformation extends AbstractEdit
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_quote_information');
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Quote & Account Information');
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-quote-information';
    }
}
