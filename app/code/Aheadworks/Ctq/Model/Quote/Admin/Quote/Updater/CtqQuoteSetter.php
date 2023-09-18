<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CtqQuoteSetter
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater
 */
class CtqQuoteSetter
{
    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteInterfaceFactory $quoteFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        QuoteRepositoryInterface $quoteRepository,
        QuoteInterfaceFactory $quoteFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Attach aw ctq quote to cart using extension attributes
     *
     * @param CartInterface $cart
     * @param array $data
     * @throws NoSuchEntityException
     */
    public function setAwCtqQuoteToCart($cart, $data)
    {
        $quoteData = $data['quote'] ?? null;
        if ($quoteData) {
            if (isset($quoteData['quote_id'])) {
                $quote = $this->quoteRepository->get($quoteData['quote_id']);
            } else {
                /** @var QuoteInterface $quote */
                $quote = $this->quoteFactory->create();
            }

            $this->dataObjectHelper->populateWithArray(
                $quote,
                $quoteData,
                QuoteInterface::class
            );

            $extensionAttributes = $cart->getExtensionAttributes()
                ? $cart->getExtensionAttributes()
                : $this->cartExtensionFactory->create();
            $extensionAttributes->setAwCtqQuote($quote);
            $cart->setExtensionAttributes($extensionAttributes);
        }
    }
}
