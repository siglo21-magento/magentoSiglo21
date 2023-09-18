<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api;

/**
 * Interface BuyerActionManagementInterface
 * @package Aheadworks\Ctq\Api
 */
interface BuyerActionManagementInterface
{
    /**
     * Retrieve available quote actions
     *
     * @param int|\Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteActionInterface[]
     */
    public function getAvailableQuoteActions($quote);
}
