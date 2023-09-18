<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Backend\Block\Widget as BackendWidget;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Magento\Backend\Block\Template\Context;
use Magento\Quote\Model\Quote;

/**
 * Class AbstractEdit
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
abstract class AbstractEdit extends BackendWidget
{
    /**
     * @var QuoteSession
     */
    protected $sessionQuote;

    /**
     * @var QuoteUpdater
     */
    protected $quoteUpdater;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Context $context
     * @param QuoteSession $sessionQuote
     * @param QuoteUpdater $quoteUpdater
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        QuoteSession $sessionQuote,
        QuoteUpdater $quoteUpdater,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->sessionQuote = $sessionQuote;
        $this->quoteUpdater = $quoteUpdater;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve quote updater model object
     *
     * @return QuoteUpdater
     */
    public function getQuoteUpdater()
    {
        return $this->quoteUpdater;
    }

    /**
     * Get session
     *
     * @return QuoteSession
     */
    protected function getSession()
    {
        return $this->sessionQuote;
    }

    /**
     * Retrieve quote model object
     *
     * @return Quote
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    /**
     * Get AW CTQ quote ID
     *
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->_request->getParam('id', null);
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getSession()->getCustomerId();
    }

    /**
     * Retrieve store model object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->getSession()->getStore();
    }

    /**
     * Retrieve store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getSession()->getStoreId();
    }

    /**
     * Retrieve formatted price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->priceCurrency->format(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getStore()
        );
    }

    /**
     * Convert price
     *
     * @param int|float $value
     * @param bool $format
     * @return string|int|float
     */
    public function convertPrice($value, $format = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $value,
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $this->getStore()
            )
            : $this->priceCurrency->convert($value, $this->getStore());
    }
}
