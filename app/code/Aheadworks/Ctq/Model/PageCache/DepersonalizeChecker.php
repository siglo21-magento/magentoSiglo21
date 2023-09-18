<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\PageCache;

use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\LayoutInterface;
use Magento\PageCache\Model\Config;
use Magento\PageCache\Model\DepersonalizeChecker as PageCacheDepersonalizeChecker;

/**
 * Class DepersonalizeChecker
 * @package Aheadworks\Ctq\Model\PageCache
 */
class DepersonalizeChecker extends PageCacheDepersonalizeChecker
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param RequestInterface $request
     * @param Manager $moduleManager
     * @param Config $cacheConfig
     * @param Checker $checker
     */
    public function __construct(
        RequestInterface $request,
        Manager $moduleManager,
        Config $cacheConfig,
        Checker $checker
    ) {
        parent::__construct($request, $moduleManager, $cacheConfig);
        $this->checker = $checker;
    }

    /**
     * {@inheritDoc}
     */
    public function checkIfDepersonalize(LayoutInterface $subject)
    {
        $result = parent::checkIfDepersonalize($subject);

        return $result && !$this->checker->isQuoteList();
    }
}
