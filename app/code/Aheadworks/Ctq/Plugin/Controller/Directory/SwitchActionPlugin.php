<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Controller\Directory;

use Aheadworks\Ctq\Model\Directory\Currency\SwitchProcessor;
use Magento\Framework\App\ActionInterface;

/**
 * Class SwitchActionPlugin
 * @package Aheadworks\Ctq\Plugin\Controller\Directory
 */
class SwitchActionPlugin
{
    /**
     * @var SwitchProcessor
     */
    private $switchProcessor;

    /**
     * @param SwitchProcessor $switchProcessor
     */
    public function __construct(
        SwitchProcessor $switchProcessor
    ) {
        $this->switchProcessor = $switchProcessor;
    }

    /**
     * Workaround solution for quote list currency
     *
     * @param ActionInterface $subject
     * @return void
     */
    public function afterExecute(ActionInterface $subject)
    {
        $this->switchProcessor->switchQuoteListCurrency();
    }
}
