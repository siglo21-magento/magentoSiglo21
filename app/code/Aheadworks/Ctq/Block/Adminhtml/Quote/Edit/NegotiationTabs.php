<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Phrase;

/**
 * Class NegotiationTabs
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class NegotiationTabs extends AbstractEdit
{
    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Comments and History');
    }

    /**
     * Get changed tab text
     *
     * @return Phrase
     */
    public function getChangedText()
    {
        return __('The information in this tab has been changed.');
    }

    /**
     * Get error tab text
     *
     * @return Phrase
     */
    public function getErrorText()
    {
        return __('This tab contains invalid data. Please resolve this before saving.');
    }

    /**
     * Get loader tab text
     *
     * @return Phrase
     */
    public function getLoaderText()
    {
        return __('Loading...');
    }
}
