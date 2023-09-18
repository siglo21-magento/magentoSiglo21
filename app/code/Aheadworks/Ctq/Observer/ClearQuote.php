<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Observer;

use Aheadworks\Ctq\Model\Quote\Cleaner;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class ClearQuote
 * @package Aheadworks\Ctq\Observer
 */
class ClearQuote implements ObserverInterface
{
    /**
     * @var Cleaner
     */
    private $cleaner;

    /**
     * @param Cleaner $cleaner
     */
    public function __construct(
        Cleaner $cleaner
    ) {
        $this->cleaner = $cleaner;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $this->cleaner->clear($request);
    }
}
