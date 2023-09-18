<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Observer;

use Aheadworks\Ctq\Model\QuoteList\MergeProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class MergeQuoteListsObserver
 * @package Aheadworks\Ctq\Observer
 */
class MergeQuoteListsObserver implements ObserverInterface
{
    /**
     * @var MergeProcessor
     */
    private $mergeProcessor;

    /**
     * @param MergeProcessor $mergeProcessor
     */
    public function __construct(
        MergeProcessor $mergeProcessor
    ) {
        $this->mergeProcessor = $mergeProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Observer $observer)
    {
        $this->mergeProcessor->mergeQuotes();
    }
}
