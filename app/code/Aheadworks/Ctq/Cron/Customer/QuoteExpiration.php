<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Cron\Customer;

use Aheadworks\Ctq\Api\QuoteExpirationManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Cron\Management;
use Aheadworks\Ctq\Model\Flag;
use Psr\Log\LoggerInterface;

/**
 * Class QuoteExpiration
 *
 * @package Aheadworks\Ctq\Cron\Customer
 */
class QuoteExpiration
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var QuoteExpirationManagementInterface
     */
    private $quoteExpirationManagement;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param QuoteExpirationManagementInterface $quoteExpirationManagement
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        QuoteExpirationManagementInterface $quoteExpirationManagement
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->quoteExpirationManagement = $quoteExpirationManagement;
    }

    /**
     * Find active quotes that are expired and mark them as expired
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_CTQ_CHECK_QUOTE_EXPIRATION_LAST_EXEC_TIME)) {
            try {
                $this->quoteExpirationManagement->processExpiredQuotes();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_CTQ_CHECK_QUOTE_EXPIRATION_LAST_EXEC_TIME);
        }
    }
}
