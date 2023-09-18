<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Export;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Composite
 * @package Aheadworks\Ctq\Model\Quote\Export
 */
class Composite
{
    /**
     * @var array
     */
    private $exporters = [];

    /**
     * @param array $exporters
     */
    public function __construct(
        array $exporters = []
    ) {
        $this->exporters = $exporters;
    }

    /**
     * Export quote
     *
     * @param QuoteInterface $quote
     * @param string $type
     * @return ResponseInterface
     * @throws \Exception
     */
    public function exportQuote($quote, $type)
    {
        $exporter = isset($this->exporters[$type]) ? $this->exporters[$type] : null;

        if ($exporter instanceof ExporterInterface) {
            return $exporter->exportQuote($quote);
        }
        
        throw new \Exception(sprintf('Unknown file type: %s requested', $type));
    }
}
