<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Controller;

use Aheadworks\Ctq\Model\Quote\Cleaner;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class FrontActionPlugin
 * @package Aheadworks\Ctq\Plugin\Controller
 */
class FrontActionPlugin
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
     * Clear cart if customer leave quote checkout
     *
     * @param ActionInterface $subject
     * @param RequestInterface $request
     * @return void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        return $this->cleaner->clear($request);
    }
}
