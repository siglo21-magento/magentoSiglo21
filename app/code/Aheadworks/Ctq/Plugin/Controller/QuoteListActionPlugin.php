<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Controller;

use Aheadworks\Ctq\Model\Request\Checker;
use Aheadworks\Ctq\Model\QuoteList\State;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QuoteListActionPlugin
 * @package Aheadworks\Ctq\Plugin\Controller
 */
class QuoteListActionPlugin
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @var State
     */
    private $state;

    /**
     * @param Checker $checker
     * @param State $state
     */
    public function __construct(
        Checker $checker,
        State $state
    ) {
        $this->checker = $checker;
        $this->state = $state;
    }

    /**
     * Emulate quote list if needed
     *
     * @param ActionInterface $subject
     * @param \Closure $proceed
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundExecute(ActionInterface $subject, \Closure $proceed)
    {
        return $this->checker->isQuoteList()
            ? $this->state->emulateQuote($proceed)
            : $proceed();
    }
}
