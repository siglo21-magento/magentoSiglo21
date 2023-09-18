<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Customer;

use Aheadworks\Ctq\Api\BuyerActionManagementInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Order\DataProvider as OrderDataProvider;
use Aheadworks\Ctq\Model\Quote\Url;
use Aheadworks\Ctq\Model\Source\Quote\Action\Type;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\Data\QuoteActionInterface;
use Aheadworks\Ctq\Model\Source\Quote\Status as StatusSource;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Quote
 * @package Aheadworks\Ctq\ViewModel\Customer
 */
class Quote implements ArgumentInterface
{
    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var BuyerActionManagementInterface
     */
    private $buyerActionManagement;

    /**
     * @var bool
     */
    private $isEditQuote;

    /**
     * @var bool
     */
    private $isAllowSorting;

    /**
     * @var OrderDataProvider
     */
    private $orderDataProvider;

    /**
     * @param StatusSource $statusSource
     * @param PriceCurrencyInterface $priceCurrency
     * @param TimezoneInterface $localeDate
     * @param UrlInterface $urlBuilder
     * @param Url $url
     * @param BuyerActionManagementInterface $buyerActionManagement
     * @param OrderDataProvider $orderDataProvider
     */
    public function __construct(
        StatusSource $statusSource,
        PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $localeDate,
        UrlInterface $urlBuilder,
        Url $url,
        BuyerActionManagementInterface $buyerActionManagement,
        OrderDataProvider $orderDataProvider
    ) {
        $this->statusSource = $statusSource;
        $this->priceCurrency = $priceCurrency;
        $this->localeDate = $localeDate;
        $this->urlBuilder = $urlBuilder;
        $this->url = $url;
        $this->buyerActionManagement = $buyerActionManagement;
        $this->orderDataProvider = $orderDataProvider;
    }

    /**
     * Retrieve formatted created at date
     *
     * @param string $createdAt
     * @return string
     */
    public function getCreatedAtFormatted($createdAt)
    {
        return $this->localeDate->formatDateTime($createdAt, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Retrieve formatted order id
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderIdFormatted($orderId)
    {
        return '#' . $this->orderDataProvider->getOrderIncrementId($orderId);
    }

    /**
     * Retrieve order url
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderUrl($orderId)
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Retrieve formatted last updated at date
     *
     * @param string $lastUpdatedAt
     * @return string
     */
    public function getLastUpdatedAtFormatted($lastUpdatedAt)
    {
        return $this->localeDate
            ->formatDateTime($lastUpdatedAt, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Retrieve formatted expired date
     *
     * @param string $expiredDate
     * @return string
     */
    public function getExpiredDateFormatted($expiredDate)
    {
        return $this->localeDate->formatDateTime($expiredDate, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
    }

    /**
     * Retrieve quote url by path
     *
     * @param string $path
     * @param int $quoteId
     * @return string
     */
    public function getQuoteUrlByPath($path, $quoteId)
    {
        return $this->urlBuilder->getUrl($path, ['quote_id' => $quoteId]);
    }

    /**
     * Retrieve formatted quote total amount
     *
     * @param float $quoteTotal
     * @return string
     */
    public function getQuoteTotalFormatted($quoteTotal)
    {
        return $this->priceCurrency->convertAndFormat($quoteTotal, false);
    }

    /**
     * Get status label
     *
     * @param string $status
     * @return string
     */
    public function getStatusLabel($status)
    {
        $statusOptions = $this->statusSource->getOptions();
        return $statusOptions[$status];
    }

    /**
     * Retrieve available quote actions
     *
     * @param QuoteInterface $quote
     * @return QuoteActionInterface[]
     */
    public function getAvailableQuoteActions($quote)
    {
        return $this->buyerActionManagement->getAvailableQuoteActions($quote->getId());
    }

    /**
     * Retrieve edit quote url
     *
     * @param int $quoteId
     * @return string
     */
    public function getEditQuoteUrl($quoteId)
    {
        return $this->url->getQuoteUrl($quoteId);
    }

    /**
     * Check if edit quote or not
     *
     * @param QuoteInterface $quote
     * @return bool
     */
    public function isEditQuote($quote)
    {
        if ($this->isEditQuote === null) {
            $this->isEditQuote = false;
            foreach ($this->getAvailableQuoteActions($quote) as $action) {
                if ($action->getType() == Type::EDIT) {
                    $this->isEditQuote = true;
                    break;
                }
            }
        }
        return $this->isEditQuote;
    }

    /**
     * Check if allow items sorting or not
     *
     * @param QuoteInterface $quote
     * @return bool
     */
    public function isAllowSorting($quote)
    {
        if ($this->isAllowSorting === null) {
            $this->isAllowSorting = false;
            foreach ($this->getAvailableQuoteActions($quote) as $action) {
                if ($action->getType() == Type::EDIT_ITEMS_ORDER) {
                    $this->isAllowSorting = true;
                    break;
                }
            }
        }
        return $this->isAllowSorting;
    }

    /**
     * Retrieve form selector
     *
     * @return string
     */
    public function getFormSelector()
    {
        return '[data-role=aw-ctq-quote-form]';
    }
}
