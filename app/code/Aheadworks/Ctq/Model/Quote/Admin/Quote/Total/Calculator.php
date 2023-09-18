<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Address;
use Magento\Catalog\Model\Product\Type as ProductType;

/**
 * Class Calculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Total
 */
class Calculator
{
    /**
     * Get subtotal
     *
     * @param Quote $quote
     * @return float
     */
    public function getSubtotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal();
    }

    /**
     * Get estimated tax total
     *
     * @param Quote $quote
     * @return float
     */
    public function getEstimatedTaxTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getTaxAmount();
    }

    /**
     * Get negotiated discount amount
     *
     * @param Quote $quote
     * @return float
     */
    public function getNegotiatedDiscountAmount($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getAwCtqAmount();
    }

    /**
     * Get discount amount
     *
     * @param Quote $quote
     * @return float
     */
    public function getDiscountAmount($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getDiscountAmount();
    }

    /**
     * Calculate item cost
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemCost(Item $item)
    {
        $totalCost = 0;
        $children = $item->getChildren();
        if (is_array($children) && count($children)) {
            foreach ($children as $child) {
                $cost = floatval($child->getProduct()->getCost());
                $totalCost += $cost * $child->getQty();
            }
            return $totalCost;
        } else {
            $totalCost = floatval($item->getProduct()->getCost());
        }

        return $totalCost;
    }

    /**
     * Calculate item cart price
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemCartPrice(Item $item)
    {
        $discount = $item->getDiscountAmount() / $item->getQty();
        return $item->getCalculationPrice() - $discount;
    }

    /**
     * Calculate item row subtotal with tax and discount
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemRowSubtotal(Item $item)
    {
        return $item->getRowTotal()
            - $item->getDiscountAmount()
            - $item->getAwCtqAmount()
            + $item->getTaxAmount()
            + $item->getDiscountTaxCompensationAmount();
    }

    /**
     * Calculate item proposed price
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemProposedPrice(Item $item)
    {
        $negotiatedDiscount = $this->getItemDiscountAmount($item) / $item->getQty();
        $cartPrice = $this->calculateItemCartPrice($item);
        return $cartPrice - $negotiatedDiscount;
    }

    /**
     * Calculate item negotiated discount
     *
     * @param Item $item
     * @return float
     */
    public function calculateItemNegotiatedDiscount(Item $item)
    {
        return $this->getItemDiscountAmount($item);
    }

    /**
     * Calculate total cost
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateTotalCost($quote)
    {
        $totalCost = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $totalCost += $this->calculateItemCost($item) * $item->getQty();
        }
        return $totalCost;
    }

    /**
     * Calculate catalog price total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateCatalogPriceTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal() + $address->getDiscountAmount();
    }

    /**
     * Calculate base catalog price total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateBaseCatalogPriceTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getBaseSubtotal() + $address->getBaseDiscountAmount();
    }

    /**
     * Calculate negotiated quote total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateNegotiatedQuoteTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal() + $address->getDiscountAmount() + $address->getAwCtqAmount() ;
    }

    /**
     * Calculate base negotiated quote total
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateBaseNegotiatedQuoteTotal($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getBaseSubtotal() + $address->getBaseDiscountAmount() + $address->getBaseAwCtqAmount() ;
    }

    /**
     * Calculate subtotal with discount and tax
     *
     * @param Quote $quote
     * @return float
     */
    public function calculateSubtotalInclDiscountAndTax($quote)
    {
        $address = $this->getQuoteAddress($quote);
        return $address->getSubtotal()
            + $address->getTaxAmount()
            + $address->getDiscountAmount()
            + $address->getDiscountTaxCompensationAmount()
            + $address->getAwCtqAmount();
    }

    /**
     * Get quote address
     *
     * @param Quote $quote
     * @return Address
     */
    private function getQuoteAddress($quote)
    {
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        return $address;
    }

    /**
     * Get discount amount for quote item
     *
     * @param Item $item
     * @return float
     */
    private function getItemDiscountAmount(Item $item)
    {
        if ($item->getProductType() != ProductType::TYPE_BUNDLE) {
            return $item->getAwCtqAmount();
        }

        $negotiatedDiscount = 0.0;
        foreach ($item->getChildren() as $child) {
            $negotiatedDiscount += $child->getAwCtqAmount();
        }

        return $negotiatedDiscount;
    }
}
