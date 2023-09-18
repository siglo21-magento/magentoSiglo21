<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\Data\ExternalQuoteCartInterface;
use Aheadworks\Ctq\Api\Data\ExternalQuoteCartInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\ExternalQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;

/**
 * Class ExternalQuoteService
 * @package Aheadworks\Ctq\Model\Service
 */
class ExternalQuoteService implements ExternalQuoteManagementInterface
{
    /**
     * @var ExternalQuoteCartInterfaceFactory
     */
    private $externalQuoteCartFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var CartItemOptionsProcessor
     */
    private $cartItemOptionsProcessor;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @param ExternalQuoteCartInterfaceFactory $externalQuoteCartFactory
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteManagement $quoteManagement
     * @param CartItemOptionsProcessor $cartItemOptionsProcessor
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     */
    public function __construct(
        ExternalQuoteCartInterfaceFactory $externalQuoteCartFactory,
        QuoteRepositoryInterface $quoteRepository,
        QuoteManagement $quoteManagement,
        CartItemOptionsProcessor $cartItemOptionsProcessor,
        BuyerQuoteManagementInterface $buyerQuoteManagement
    ) {
        $this->externalQuoteCartFactory = $externalQuoteCartFactory;
        $this->quoteRepository = $quoteRepository;
        $this->quoteManagement = $quoteManagement;
        $this->cartItemOptionsProcessor = $cartItemOptionsProcessor;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function get($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);

        return $this->prepareQuoteCart($quote);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $items = [];

        foreach ($this->quoteRepository->getList($searchCriteria)->getItems() as $item) {
            $items[] = $this->prepareQuoteCart($item);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function copyQuote($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $copiedQuote = $this->buyerQuoteManagement->copyQuote($quote);

        return $this->prepareQuoteCart($copiedQuote);
    }

    /**
     * Prepare external quote cart
     *
     * @param QuoteInterface $quote
     * @return QuoteInterface
     * @throws LocalizedException
     */
    private function prepareQuoteCart($quote)
    {
        /** @var ExternalQuoteCartInterface $quoteCart */
        $quoteCart = $this->externalQuoteCartFactory->create();
        $cart = $this->quoteManagement->getCartByQuoteId($quote->getId());
        $items = [];

        foreach ($cart->getAllVisibleItems() as $item) {
            $item = $this->cartItemOptionsProcessor->addProductOptions($item->getProductType(), $item);
            $items[] = $this->cartItemOptionsProcessor->applyCustomOptions($item);
        }

        $quoteCart
            ->setQuote($cart)
            ->setItems($items)
            ->setBillingAddress($cart->getBillingAddress())
            ->setShippingAddress($cart->getShippingAddress());
        $quote->setCart($quoteCart);

        return $quote;
    }
}
