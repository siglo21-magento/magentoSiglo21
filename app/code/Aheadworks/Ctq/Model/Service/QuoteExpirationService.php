<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\QuoteExpirationManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Quote\Expiration\Finder as ExpiredQuoteFinder;
use Aheadworks\Ctq\Model\Quote\Expiration\Notifier;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\Status as ReminderStatus;
use Aheadworks\Ctq\Model\Source\Quote\Status as QuoteStatus;

/**
 * Class QuoteExpirationService
 *
 * @package Aheadworks\Ctq\Model\Service
 */
class QuoteExpirationService implements QuoteExpirationManagementInterface
{
    /**
     * @var SellerQuoteManagementInterface
     */
    private $sellerQuoteService;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var ExpiredQuoteFinder
     */
    private $quoteFinder;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @param QuoteRepositoryInterface $quoteRepository
     * @param SellerQuoteManagementInterface $sellerQuoteService
     * @param ExpiredQuoteFinder $quoteFinder
     * @param Notifier $notifier
     */
    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        SellerQuoteManagementInterface $sellerQuoteService,
        ExpiredQuoteFinder $quoteFinder,
        Notifier $notifier
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->sellerQuoteService = $sellerQuoteService;
        $this->quoteFinder = $quoteFinder;
        $this->notifier = $notifier;
    }

    /**
     * @inheritdoc
     */
    public function processExpiredQuotes()
    {
        $quotes = $this->quoteFinder->findExpiredQuotes();
        foreach ($quotes as $quote) {
            $this->sellerQuoteService->changeStatus($quote->getId(), QuoteStatus::EXPIRED);
        }
    }

    /**
     * @inheritdoc
     */
    public function processExpirationReminder()
    {
        $quotes = $this->quoteFinder->findQuotesThatGetExpiredSoon();
        foreach ($quotes as $quote) {
            $notified = $this->notifier->notify($quote);
            $reminderStatus = $notified ? ReminderStatus::SENT : ReminderStatus::FAILED;
            $quote->setReminderStatus($reminderStatus);
            $this->quoteRepository->save($quote);
        }
    }
}
