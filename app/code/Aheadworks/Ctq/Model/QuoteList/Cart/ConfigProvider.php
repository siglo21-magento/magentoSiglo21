<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\QuoteList\Cart;

use Aheadworks\Ctq\Model\QuoteList\Provider;
use Aheadworks\Ctq\Model\QuoteList\State;
use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class ConfigProvider
 * @package Aheadworks\Ctq\Model\QuoteList\Cart
 */
class ConfigProvider extends CompositeConfigProvider
{
    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * @var Quote
     */
    private $_quote;

    /**
     * @var Provider
     */
    private $quoteProvider;

    /**
     * @var State
     */
    private $state;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param CheckoutSession $session
     * @param Provider $quoteProvider
     * @param State $state
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param array $configProviders
     */
    public function __construct(
        CheckoutSession $session,
        Provider $quoteProvider,
        State $state,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        array $configProviders
    ) {
        parent::__construct($configProviders);
        $this->session = $session;
        $this->quoteProvider = $quoteProvider;
        $this->state = $state;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        $this->_quote = $this->session->getQuote();
        if ($this->quoteProvider->getQuoteId()) {
            $config = $this->state->emulateQuote([$this, 'parent::getConfig']);

            return $this->prepareConfig($config);
        } else {
            return parent::getConfig();
        }
    }

    /**
     * Set quote list flag to checkout config
     *
     * @param array $config
     * @return array
     * @throws LocalizedException
     */
    private function prepareConfig($config)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $config['isCustomerLoggedIn'] = false;
        $config['isQuoteList'] = true;
        $config['quoteData']['entity_id'] = $quoteIdMask->load(
            $this->quoteProvider->getQuoteId(),
            'quote_id'
        )->getMaskedId();

        return $config;
    }
}
