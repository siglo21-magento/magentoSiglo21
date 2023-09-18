<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Cart\ExtensionAttributes;

use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class JoinAttributeProcessor
 * @package Aheadworks\Ctq\Model\Cart\ExtensionAttributes
 */
class JoinAttributeProcessor
{
    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Attach quote data to cart object
     *
     * @param CartInterface $cart
     * @return CartInterface
     */
    public function process($cart)
    {
        try {
            $quote = $this->quoteRepository->getByCartId($cart->getId());

            $extensionAttributes = $cart->getExtensionAttributes()
                ? $cart->getExtensionAttributes()
                : $this->cartExtensionFactory->create();
            $extensionAttributes->setAwCtqQuote($quote);
            $cart->setExtensionAttributes($extensionAttributes);
        } catch (NoSuchEntityException $e) {
        }

        return $cart;
    }
}
