<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider as QuoteDataProvider;

/**
 * Class Common
 * @package Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider
 */
class Common implements ConfigProviderInterface
{
    /**
     * @var QuoteDataProvider
     */
    private $quoteDataProvider;

    /**
     * @var LocaleFormat
     */
    private $localeFormat;

    /**
     * @param QuoteDataProvider $quoteDataProvider
     * @param LocaleFormat $localeFormat
     */
    public function __construct(
        QuoteDataProvider $quoteDataProvider,
        LocaleFormat $localeFormat
    ) {
        $this->quoteDataProvider = $quoteDataProvider;
        $this->localeFormat = $localeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $cart = $this->quoteDataProvider->getCart();

        $output['quoteData'] = $cart->toArray();
        $output['basePriceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $cart->getBaseCurrencyCode()
        );
        $output['priceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $cart->getQuoteCurrencyCode()
        );
        $output['storeCode'] = $cart->getStore()->getCode();

        return $output;
    }
}
