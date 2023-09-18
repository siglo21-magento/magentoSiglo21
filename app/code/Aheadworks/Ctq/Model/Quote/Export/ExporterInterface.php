<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Export;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ExporterInterface
 * @package Aheadworks\Ctq\Model\Quote\Export
 */
interface ExporterInterface
{
    /**
     * Export quote
     *
     * @param QuoteInterface $quote
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function exportQuote($quote);
}
