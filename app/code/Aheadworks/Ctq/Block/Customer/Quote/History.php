<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Customer\Quote;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Block\History\Render;
use Aheadworks\Ctq\Block\Html\Pager;
use Magento\Framework\View\Element\Template;

/**
 * Class History
 * @package Aheadworks\Ctq\Block\Customer\Quote
 * @method \Aheadworks\Ctq\ViewModel\History\HistoryList getHistoryListViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider getDataProviderViewModel()
 */
class History extends Template
{
    /**
     * Return the history item html
     *
     * @param HistoryInterface $history
     * @return string
     */
    public function getHistoryItemHtml($history)
    {
        /** @var Render $block */
        $block = $this->getLayout()->getBlock('aw_ctq.quote.history.render');
        $block->setHistory($history);

        return $block->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var Pager $pager */
        $pager = $this->getLayout()->createBlock(
            Pager::class,
            'aw_ctq.customer.quote.history.pager'
        );

        $quoteId = $this->getDataProviderViewModel()->getQuote()->getId();
        $this->getHistoryListViewModel()
            ->getSearchCriteriaBuilder()
            ->setCurrentPage($pager->getCurrentPage())
            ->setPageSize($pager->getLimit());
        $historySearchResults = $this->getHistoryListViewModel()->getHistorySearchResults($quoteId);
        if ($historySearchResults) {
            $pager->setSearchResults($historySearchResults);
            $this->setChild('pager', $pager);
        }

        return $this;
    }
}
