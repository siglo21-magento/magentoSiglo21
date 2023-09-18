<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Shipping\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\AbstractEdit;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Tax\Helper\Data as TaxData;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as SessionQuote;
use Magento\Backend\Block\Template\Context;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

/**
 * Class Form
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Shipping\Method
 * @method \Aheadworks\Ctq\ViewModel\Quote\Edit\CurrentQuote getQuoteViewModel()
 */
class Form extends AbstractEdit
{
    /**
     * @var array
     */
    private $rates;

    /**
     * @var TaxData
     */
    private $taxData;

    /**
     * @param Context $context
     * @param SessionQuote $sessionQuote
     * @param QuoteUpdater $quoteUpdater
     * @param PriceCurrencyInterface $priceCurrency
     * @param TaxData $taxData
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionQuote $sessionQuote,
        QuoteUpdater $quoteUpdater,
        PriceCurrencyInterface $priceCurrency,
        TaxData $taxData,
        array $data = []
    ) {
        $this->taxData = $taxData;
        parent::__construct($context, $sessionQuote, $quoteUpdater, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_shipping_method_form');
    }

    /**
     * Retrieve quote shipping address model
     *
     * @return QuoteAddress
     */
    public function getAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Retrieve array of shipping rates groups
     *
     * @return array
     */
    public function getShippingRates()
    {
        if (empty($this->rates)) {
            $this->rates = $this->getAddress()->getGroupedAllShippingRates();
        }
        return $this->rates;
    }

    /**
     * Retrieve carrier name from store configuration
     *
     * @param string $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->_scopeConfig->getValue(
            'carriers/' . $carrierCode . '/title',
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        )
        ) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * Retrieve current selected shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * Check activity of method by code
     *
     * @param string $code
     * @return bool
     */
    public function isMethodActive($code)
    {
        return $code === $this->getShippingMethod();
    }

    /**
     * Get shipping price including tax
     *
     * @param Rate $rate
     * @return float
     */
    public function getShippingPriceInclTax($rate)
    {
        return $this->getShippingPrice(
            $rate->getPrice(),
            $this->taxData->displayShippingPriceIncludingTax()
        );
    }

    /**
     * Get shipping price excluding tax
     *
     * @param Rate $rate
     * @return float
     */
    public function getShippingPriceExclTax($rate)
    {
        return $this->getShippingPrice($rate->getPrice(), true);
    }

    /**
     * Is need to display both prices
     *
     * @param Rate $rate
     * @return bool
     */
    public function isNeedToDisplayBothPrices($rate)
    {
        return $this->taxData->displayShippingBothPrices()
        && ($this->getShippingPriceExclTax($rate) != $this->getShippingPriceInclTax($rate));
    }

    /**
     * Is current quote virtual
     *
     * @return bool
     */
    public function isQuoteVirtual()
    {
        return $this->getQuote()->isVirtual();
    }

    /**
     * Retrieve rate of active shipping method
     *
     * @return Rate|false
     */
    public function getActiveMethodRate()
    {
        $rates = $this->getShippingRates();
        if (is_array($rates)) {
            foreach ($rates as $group) {
                foreach ($group as $rate) {
                    if ($rate->getCode() == $this->getShippingMethod()) {
                        return $rate;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get rate request
     *
     * @return mixed
     */
    public function getIsRateRequest()
    {
        return $this->getRequest()->getParam('collect_shipping_rates');
    }

    /**
     * Get shipping price
     *
     * @param float $price
     * @param bool $flag
     * @return float
     */
    public function getShippingPrice($price, $flag)
    {
        return $this->priceCurrency->convertAndFormat(
            $this->taxData->getShippingPrice(
                $price,
                $flag,
                $this->getAddress(),
                null,
                $this->getAddress()->getQuote()->getStore()
            ),
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getQuote()->getStore()
        );
    }
}
