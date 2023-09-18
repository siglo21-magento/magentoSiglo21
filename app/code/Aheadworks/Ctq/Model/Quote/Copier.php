<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Aheadworks\Ctq\Model\Quote\Cart\ToNativeCart;

/**
 * Class Copier
 *
 * @package Aheadworks\Ctq\Model\Quote
 */
class Copier
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var ToNativeCart
     */
    private $toNativeCartConverter;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param QuoteInterfaceFactory $quoteFactory
     * @param ToNativeCart $toNativeCartConverter
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        QuoteInterfaceFactory $quoteFactory,
        ToNativeCart $toNativeCartConverter
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->quoteFactory = $quoteFactory;
        $this->toNativeCartConverter = $toNativeCartConverter;
    }

    /**
     * Copy and get new cart
     *
     * @param QuoteCartInterface $cart
     * @return CartInterface
     * @throws LocalizedException
     */
    public function copyCart(QuoteCartInterface $cart)
    {
        return $this->toNativeCartConverter->convert($cart, true);
    }

    /**
     * Copy and get new quote
     *
     * @param QuoteInterface $quote
     * @return QuoteInterface
     */
    public function copyQuote(QuoteInterface $quote)
    {
        $newQuoteData = $this->prepareNewQuoteData($quote);
        $newQuote = $this->convertQuoteDataToObject($newQuoteData);

        return $newQuote;
    }

    /**
     * Prepare data for new quote
     *
     * @param QuoteInterface $quote
     * @return array
     */
    private function prepareNewQuoteData(QuoteInterface $quote)
    {
        $quoteData = $this->dataObjectProcessor->buildOutputDataArray(
            $quote,
            QuoteInterface::class
        );

        unset($quoteData[QuoteInterface::ID]);
        unset($quoteData[QuoteInterface::CART]);
        unset($quoteData[QuoteInterface::CART_ID]);
        unset($quoteData[QuoteInterface::CREATED_AT]);
        unset($quoteData[QuoteInterface::LAST_UPDATED_AT]);
        unset($quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_TYPE]);
        unset($quoteData[QuoteInterface::NEGOTIATED_DISCOUNT_VALUE]);
        return $quoteData;
    }

    /**
     * Convert quote data to object
     *
     * @param array $data
     * @return QuoteInterface
     */
    private function convertQuoteDataToObject($data)
    {
        /** @var QuoteInterface $object */
        $object = $this->quoteFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $data,
            QuoteInterface::class
        );

        return $object;
    }
}
