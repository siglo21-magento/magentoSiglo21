<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\View\Element\Messages as ElementMessages;

/**
 * Class Messages
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class Messages extends ElementMessages
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->messageManager->getMessages(true));
        parent::_prepareLayout();
    }
}
