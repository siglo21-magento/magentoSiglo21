<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\TotalFactory;
use Magento\Quote\Model\Quote\Address\Total;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Provider\ArrayRetriever;

/**
 * Class Provider
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Total
 */
class Provider
{
    /**
     * @var TotalFactory
     */
    private $totalFactory;

    /**
     * @var Calculator
     */
    private $totalCalculator;

    /**
     * @var ArrayRetriever
     */
    private $arrayRetriever;

    /**
     * @param TotalFactory $totalFactory
     * @param Calculator $totalCalculator
     * @param ArrayRetriever $arrayRetriever
     */
    public function __construct(
        TotalFactory $totalFactory,
        Calculator $totalCalculator,
        ArrayRetriever $arrayRetriever
    ) {
        $this->totalFactory = $totalFactory;
        $this->totalCalculator = $totalCalculator;
        $this->arrayRetriever = $arrayRetriever;
    }

    /**
     * Get totals
     *
     * @param Quote $quote
     * @return array
     */
    public function getQuoteTotals($quote)
    {
        $quoteTotals = $this->getOrderTotals($quote);
        $negotiatedDiscount = $this->arrayRetriever->retrieveByKey('aw_ctq', $quoteTotals);
        $tax = $this->arrayRetriever->retrieveByKey('tax', $quoteTotals);
        $grandTotal = $this->arrayRetriever->retrieveByKey('grand_total', $quoteTotals);
        $shipping = $this->arrayRetriever->retrieveByKey('shipping', $quoteTotals);

        $totals[] = $this->getCostTotal($quote);
        $totals[] = $this->arrayRetriever->retrieveByKey('subtotal', $quoteTotals);
        $totals[] = $this->arrayRetriever->retrieveByKey('discount', $quoteTotals);
        $totals[] = $this->getCatalogPriceTotal($quote);
        $totals[] = $this->moveToArea($negotiatedDiscount);
        $totals[] = $this->moveToArea($this->getNegotiatedQuoteTotal($quote));
        $totals[] = $this->moveToArea($shipping);
        $totals[] = $this->moveToArea($tax);
        foreach ($quoteTotals as $total) {
            $totals[] = $this->moveToArea($total);
        }
        $totals[] = $this->moveToArea($grandTotal);

        return array_filter($totals);
    }

    /**
     * Get quote calculated native totals
     *
     * @param Quote $quote
     * @return array
     */
    public function getOrderTotals($quote)
    {
        if ($quote->isVirtual()) {
            $totals = $quote->getBillingAddress()->getTotals();
        } else {
            $totals = $quote->getShippingAddress()->getTotals();
        }

        return $totals;
    }

    /**
     * Get cost total
     *
     * @param Quote $quote
     * @return Total
     */
    private function getCostTotal($quote)
    {
        $totalData = [
            'value' => $this->totalCalculator->calculateTotalCost($quote),
            'code' => 'total_cost',
            'title' => __('Total Cost')
        ];
        $total = $this->totalFactory->create();
        $total->setData($totalData);

        return $total;
    }

    /**
     * Get catalog price total
     *
     * @param Quote $quote
     * @return Total
     */
    private function getCatalogPriceTotal($quote)
    {
        $totalData = [
            'value' => $this->totalCalculator->calculateCatalogPriceTotal($quote),
            'code' => 'catalog_total_price_excl_tax',
            'title' => __('Catalog Total Price (Excl. Tax)')
        ];
        $total = $this->totalFactory->create();
        $total->setData($totalData);

        return $total;
    }

    /**
     * Get negotiated quote total
     *
     * @param Quote $quote
     * @return Total
     */
    private function getNegotiatedQuoteTotal($quote)
    {
        $totalData = [
            'value' => $this->totalCalculator->calculateNegotiatedQuoteTotal($quote),
            'code' => 'negotiated_subtotal_excl_tax',
            'title' => __('Quote Subtotal (Excl. Tax)')
        ];
        $total = $this->totalFactory->create();
        $total->setData($totalData);

        return $total;
    }

    /**
     * Move total to specified area
     *
     * @param Total|null $total
     * @param string $area
     * @return Total
     */
    private function moveToArea($total, $area = 'footer')
    {
        if ($total) {
            $total->setArea($area);
        }
        return $total;
    }
}
