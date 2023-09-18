<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository;

use Aheadworks\Ctq\Model\Cart\ExtensionAttributes\JoinAttributeProcessor;
use Magento\Quote\Model\QuoteRepository\LoadHandler;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class LoadHandlerPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository
 */
class LoadHandlerPlugin
{
    /**
     * @var JoinAttributeProcessor
     */
    private $joinAttributeProcessor;

    /**
     * @param JoinAttributeProcessor $joinAttributeProcessor
     */
    public function __construct(
        JoinAttributeProcessor $joinAttributeProcessor
    ) {
        $this->joinAttributeProcessor = $joinAttributeProcessor;
    }

    /**
     * Attach quote data to cart extension attribute
     *
     * @param LoadHandler $subject
     * @param CartInterface $quote
     * @return CartInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoad($subject, CartInterface $quote)
    {
        $quote = $this->joinAttributeProcessor->process($quote);

        return $quote;
    }
}
