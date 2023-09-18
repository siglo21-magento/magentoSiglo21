<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Metadata\Negotiation;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class DiscountFactory
 *
 * @package Aheadworks\Ctq\Model\Metadata\Negotiation
 */
class DiscountFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create negotiated discount object
     *
     * @param CartInterface $cart
     * @return NegotiatedDiscountInterface
     */
    public function create($cart)
    {
        $data = $this->prepareData($cart);
        return $this->objectManager->create(NegotiatedDiscountInterface::class, ['data' => $data]);
    }

    /**
     * Prepare data
     *
     * @param CartInterface $cart
     * @return array
     */
    private function prepareData($cart)
    {
        /** @var QuoteInterface $quote */
        $quote = $cart->getExtensionAttributes()->getAwCtqQuote();

        return [
            NegotiatedDiscountInterface::DISCOUNT_TYPE => $quote->getNegotiatedDiscountType(),
            NegotiatedDiscountInterface::DISCOUNT_VALUE => $quote->getNegotiatedDiscountValue(),
        ];
    }
}
