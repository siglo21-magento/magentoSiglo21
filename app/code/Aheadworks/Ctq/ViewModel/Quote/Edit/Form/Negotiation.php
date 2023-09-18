<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Quote\Edit\Form;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Model\Source\Quote\Negotiation\DiscountType;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Calculator as TotalCalculator;

/**
 * Class Negotiation
 *
 * @package Aheadworks\Ctq\ViewModel\Quote\Edit\Form
 */
class Negotiation implements ArgumentInterface
{
    /**
     * @var DiscountType
     */
    private $discountTypeSource;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var TotalCalculator
     */
    private $totalCalculator;

    /**
     * @param DiscountType $discountTypeSource
     * @param PriceCurrencyInterface $priceCurrency
     * @param TotalCalculator $totalCalculator
     */
    public function __construct(
        DiscountType $discountTypeSource,
        PriceCurrencyInterface $priceCurrency,
        TotalCalculator $totalCalculator
    ) {
        $this->discountTypeSource = $discountTypeSource;
        $this->priceCurrency = $priceCurrency;
        $this->totalCalculator = $totalCalculator;
    }

    /**
     * Get discount type config
     *
     * @param Quote $cart
     * @return array
     */
    public function getDiscountTypeConfig($cart)
    {
        $options =  $this->discountTypeSource->getOptions();
        $rules = $this->prepareValidationRules($cart);
        return array_merge_recursive($options, $rules);
    }

    /**
     * Retrieve currency symbol
     *
     * @param Quote $quote
     * @return string
     */
    public function getCurrencySymbol($quote)
    {
        return $this->priceCurrency->getCurrencySymbol(null, $quote->getCurrency()->getBaseCurrencyCode());
    }

    /**
     * Get checked attribute for discount type radio button if selected
     *
     * @param Quote $cart
     * @param string $type
     * @return string
     */
    public function getDiscountTypeChecked($cart, $type)
    {
        $checked = '';
        if ($cart->getExtensionAttributes() && $cart->getExtensionAttributes()->getAwCtqQuote()) {
            /** @var QuoteInterface $quote */
            $quote = $cart->getExtensionAttributes()->getAwCtqQuote();
            if ($quote->getNegotiatedDiscountType() == $type) {
                $checked = 'checked';
            }
        }

        return $checked;
    }

    /**
     * Get discount value if specified
     *
     * @param Quote $cart
     * @param string $type
     * @return string
     */
    public function getDiscountValue($cart, $type)
    {
        $value = '';
        if ($cart->getExtensionAttributes() && $cart->getExtensionAttributes()->getAwCtqQuote()) {
            /** @var QuoteInterface $quote */
            $quote = $cart->getExtensionAttributes()->getAwCtqQuote();
            if ($quote->getNegotiatedDiscountType() == $type) {
                $value = $quote->getNegotiatedDiscountValue();
            }
        }

        return $value;
    }

    /**
     * Is need to display currency symbol
     *
     * @param string $type
     * @return string
     */
    public function isNeedToDisplayCurrencySymbol($type)
    {
        return $type != DiscountType::PERCENTAGE_DISCOUNT;
    }

    /**
     * Is need to display currency symbol
     *
     * @param Quote $cart
     * @return float
     */
    public function getCartTotal($cart)
    {
        return $this->totalCalculator->calculateCatalogPriceTotal($cart);
    }

    /**
     * Get validation rule for discount type
     *
     * @param array $config
     * @return string
     */
    public function getValidationRuleForDiscountInput($config)
    {
        $rule = '';
        if (isset($config['validation-rules']) && is_array($config['validation-rules'])) {
            foreach ($config['validation-rules'] as $ruleName => $params) {
                $rule =  $rule . ' "' . $ruleName . '":"' . $params . '"';
            }
        }
        return $rule;
    }

    /**
     * Prepare validation rules for each discount type option
     *
     * @param Quote $cart
     * @return array
     */
    private function prepareValidationRules($cart)
    {
        return [
            DiscountType::PERCENTAGE_DISCOUNT => [
                'validation-rules' => ['validate-number-range' => '0-100']
            ],
            DiscountType::AMOUNT_DISCOUNT => [
                'validation-rules' => ['validate-number-range' => '0-' . $this->getCartTotal($cart)]
            ],
            DiscountType::PROPOSED_PRICE => [
                'validation-rules' => ['validate-number-range' => '0-' . $this->getCartTotal($cart)]
            ]
        ];
    }
}
