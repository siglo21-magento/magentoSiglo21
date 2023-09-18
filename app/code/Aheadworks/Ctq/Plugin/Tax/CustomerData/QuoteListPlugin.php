<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Tax\CustomerData;

use Aheadworks\Ctq\Model\QuoteList\Permission\Checker;
use Aheadworks\Ctq\Model\QuoteList\Provider;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use Magento\Tax\Plugin\Checkout\CustomerData\Cart;
use Magento\Tax\Block\Item\Price\Renderer;

/**
 * Class QuoteListPlugin
 * @package Aheadworks\Ctq\Plugin\Tax\CustomerData
 */
class QuoteListPlugin extends Cart
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @var CartInterfaceFactory
     */
    private $cartFactory;

    /**
     * @param Session $checkoutSession
     * @param Data $checkoutHelper
     * @param Renderer $itemPriceRenderer
     * @param Provider $provider
     * @param Checker $checker
     * @param CartInterfaceFactory $cartFactory
     */
    public function __construct(
        Session $checkoutSession,
        Data $checkoutHelper,
        Renderer $itemPriceRenderer,
        Provider $provider,
        Checker $checker,
        CartInterfaceFactory $cartFactory
    ) {
        parent::__construct($checkoutSession, $checkoutHelper, $itemPriceRenderer);
        $this->provider = $provider;
        $this->checker = $checker;
        $this->cartFactory = $cartFactory;
    }

    /**
     * @inheritDoc
     */
    protected function getQuote()
    {
        return $this->checker->isAllowed()
            ? $this->provider->getQuote()
            : $this->cartFactory->create();
    }
}
