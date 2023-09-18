<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Directory\Currency;

use Aheadworks\Ctq\Model\QuoteList\Provider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class SwitchProcessor
 * @package Aheadworks\Ctq\Model\Directory\Currency
 */
class SwitchProcessor
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param Provider $provider
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Provider $provider
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->provider = $provider;
    }

    /**
     * Switch quote list currency
     */
    public function switchQuoteListCurrency()
    {
        try {
            $quote = $this->provider->getQuote();
            if ($quote) {
                $this->quoteRepository->save($quote->collectTotals());
            }
        } catch (LocalizedException $e) {
        }
    }
}
