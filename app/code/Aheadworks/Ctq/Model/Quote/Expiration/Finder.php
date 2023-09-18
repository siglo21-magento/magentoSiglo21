<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Expiration;

use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\Source\Quote\Status as QuoteStatus;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\Status as ReminderStatus;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Finder
 *
 * @package Aheadworks\Ctq\Model\Quote\Expiration
 */
class Finder
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param QuoteRepositoryInterface $quoteRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param Config $config
     */
    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        Config $config
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->config = $config;
    }

    /**
     * Retrieve expired quotes
     *
     * @return QuoteInterface[]
     * @throws LocalizedException
     * @throws \Exception
     */
    public function findExpiredQuotes()
    {
        $stores = $this->storeManager->getStores();
        $expiredQuotes = [];
        foreach ($stores as $store) {
            $this->searchCriteriaBuilder
                ->addFilter(QuoteInterface::STORE_ID, $store->getId())
                ->addFilter(
                    QuoteInterface::STATUS,
                    [QuoteStatus::PENDING_BUYER_REVIEW, QuoteStatus::PENDING_SELLER_REVIEW],
                    'in'
                )
                ->addFilter(QuoteInterface::EXPIRATION_DATE, $this->getCurrentDate($store), 'lt');

            $quotes = $this->quoteRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();

            $expiredQuotes = array_merge($expiredQuotes, $quotes);
        }

        return $expiredQuotes;
    }

    /**
     * Retrieve quotes that get expired soon
     *
     * @return QuoteInterface[]
     * @throws LocalizedException
     * @throws \Exception
     */
    public function findQuotesThatGetExpiredSoon()
    {
        $stores = $this->storeManager->getStores();
        $readyToExpireQuotes = [];

        // select quotes with not given reminder date
        $this->searchCriteriaBuilder
            ->addFilter(
                QuoteInterface::REMINDER_STATUS,
                [ReminderStatus::READY_TO_BE_SENT, ReminderStatus::FAILED],
                'in'
            )
            ->addFilter(
                QuoteInterface::STATUS,
                [QuoteStatus::PENDING_BUYER_REVIEW, QuoteStatus::PENDING_SELLER_REVIEW],
                'in'
            )
            ->addFilter('with_not_given_date_reminder', null);

        $quotes = $this->quoteRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        $readyToExpireQuotes = array_merge($readyToExpireQuotes, $quotes);

        // select quotes with given reminder date
        foreach ($stores as $store) {
            $reminderDaysOffset = $this->config->getSendEmailReminderInDays($store->getId());
            if (!$reminderDaysOffset) {
                continue;
            }

            $this->searchCriteriaBuilder
                ->addFilter(QuoteInterface::STORE_ID, $store->getId())
                ->addFilter(
                    QuoteInterface::REMINDER_STATUS,
                    [ReminderStatus::READY_TO_BE_SENT, ReminderStatus::FAILED],
                    'in'
                )
                ->addFilter(
                    QuoteInterface::STATUS,
                    [QuoteStatus::PENDING_BUYER_REVIEW, QuoteStatus::PENDING_SELLER_REVIEW],
                    'in'
                )
                ->addFilter('with_given_date_reminder', $this->getCurrentDate($store, $reminderDaysOffset));

            $quotes = $this->quoteRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();

            $readyToExpireQuotes = array_merge($readyToExpireQuotes, $quotes);
        }

        return $readyToExpireQuotes;
    }

    /**
     * Get current date
     *
     * @param StoreInterface $store
     * @param int|null $daysOffset
     * @return string
     * @throws \Exception
     */
    public function getCurrentDate($store, $daysOffset = null)
    {
        $timezone = $this->localeDate->getConfigTimezone(ScopeInterface::SCOPE_STORE, $store->getCode());
        $now = new \DateTime(null, new \DateTimeZone($timezone));
        if ($daysOffset) {
            $now->add(new \DateInterval('P' . $daysOffset . 'D'));
        }
        $now->setTimezone(new \DateTimeZone('UTC'));
        $now->setTime(00, 00, 00);

        return $now->format(StdlibDateTime::DATETIME_PHP_FORMAT);
    }
}
