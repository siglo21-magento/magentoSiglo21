<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\NegotiationTabs;

use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\AbstractEdit;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Block\History\Render;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class History
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\NegotiationTabs
 */
class History extends AbstractEdit
{
    /**
     * Return the history item html
     *
     * @param HistoryInterface $history
     * @return string
     * @throws LocalizedException
     */
    public function getHistoryItemHtml($history)
    {
        /** @var Render $block */
        $block = $this->getLayout()->getBlock('aw_ctq.quote.history.render');
        $block->setHistory($history);

        return $block->toHtml();
    }
}
