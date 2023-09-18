<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface QuoteExpirationManagementInterface
 * @api
 */
interface QuoteExpirationManagementInterface
{
    /**
     * Check expiration period and mark quotes as expired
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processExpiredQuotes();

    /**
     * Process expiration reminder
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processExpirationReminder();
}
