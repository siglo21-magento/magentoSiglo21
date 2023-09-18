<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Customer;

use Magento\Theme\Block\Html\Pager;

/**
 * Class Quotes
 * @package Aheadworks\Ctq\Block\Customer
 * @method \Aheadworks\Ctq\ViewModel\Customer\QuoteList getQuoteListViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote getQuoteViewModel()
 */
class QuoteList extends Quote
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getQuoteListViewModel()->getQuoteList()) {
            /** @var Pager $pager */
            $pager = $this->getLayout()
                ->createBlock(
                    Pager::class,
                    'aw_ctq.customer.quote.list.pager'
                );
            $pager->setCollection($this->getQuoteListViewModel()->getQuoteList());
            $this->setChild('pager', $pager);
            $this->getQuoteListViewModel()->getQuoteList()->load();
        }
        return $this;
    }
}
