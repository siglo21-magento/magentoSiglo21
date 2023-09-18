<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider as QuoteDataProvider;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Quote\Model\Cart\TotalsConverter;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\TotalsInterfaceFactory;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;

/**
 * Class Totals
 * @package Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider
 */
class Totals implements ConfigProviderInterface
{
    /**
     * @var QuoteDataProvider
     */
    private $quoteDataProvider;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ItemConverter
     */
    private $itemConverter;

    /**
     * @var TotalsConverter
     */
    private $totalsConverter;

    /**
     * @var TotalsInterfaceFactory
     */
    private $totalsFactory;

    /**
     * @param QuoteDataProvider $quoteDataProvider
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ItemConverter $itemConverter
     * @param TotalsConverter $totalsConverter
     * @param TotalsInterfaceFactory $totalsFactory
     */
    public function __construct(
        QuoteDataProvider $quoteDataProvider,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        ItemConverter $itemConverter,
        TotalsConverter $totalsConverter,
        TotalsInterfaceFactory $totalsFactory
    ) {
        $this->quoteDataProvider = $quoteDataProvider;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->itemConverter = $itemConverter;
        $this->totalsConverter = $totalsConverter;
        $this->totalsFactory = $totalsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $cart = $this->quoteDataProvider->getCart();

        $output['totalsData'] = $this->processTotals($cart);

        return $output;
    }

    /**
     * Convert cart object to totals array
     *
     * @param Quote $cart
     * @return array
     * @throws \Exception
     */
    private function processTotals($cart)
    {
        if ($cart->isVirtual()) {
            $addressTotalsData = $cart->getBillingAddress()->getData();
            $addressTotals = $cart->getBillingAddress()->getTotals();
        } else {
            $addressTotalsData = $cart->getShippingAddress()->getData();
            $addressTotals = $cart->getShippingAddress()->getTotals();
        }
        unset($addressTotalsData[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        /** @var \Magento\Quote\Api\Data\TotalsInterface $quoteTotals */
        $quoteTotals = $this->totalsFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteTotals,
            $addressTotalsData,
            TotalsInterface::class
        );

        $items = [];
        foreach ($cart->getAllVisibleItems() as $index => $item) {
            $items[$index] = $this->itemConverter->modelToDataObject($item);
        }
        $calculatedTotals = $this->totalsConverter->process($addressTotals);
        $quoteTotals->setTotalSegments($calculatedTotals);

        $amount = $quoteTotals->getGrandTotal() - $quoteTotals->getTaxAmount();
        $amount = $amount > 0 ? $amount : 0;
        $quoteTotals->setCouponCode($cart->getCouponCode());
        $quoteTotals->setGrandTotal($amount);
        $quoteTotals->setItems($items);
        $quoteTotals->setItemsQty($cart->getItemsQty());
        $quoteTotals->setBaseCurrencyCode($cart->getBaseCurrencyCode());
        $quoteTotals->setQuoteCurrencyCode($cart->getQuoteCurrencyCode());

        $quoteTotalsData = $this->dataObjectProcessor->buildOutputDataArray(
            $quoteTotals,
            TotalsInterface::class
        );

        return $quoteTotalsData;
    }
}
