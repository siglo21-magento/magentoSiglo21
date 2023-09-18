<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\Data\CartInterface as CtqCartInterface;
use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Copier;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\Status as ExpirationReminderStatus;
use Aheadworks\Ctq\Model\Quote\Expiration\Calculator as ExpirationCalculator;

/**
 * Class SellerQuoteService
 * @package Aheadworks\Ctq\Model\Service
 */
class SellerQuoteService implements SellerQuoteManagementInterface
{
    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Copier
     */
    private $quoteCopier;

    /**
     * @var ExpirationCalculator
     */
    private $expirationCalculator;

    /**
     * @param QuoteManagement $quoteManagement
     * @param CartRepositoryInterface $cartRepository
     * @param Copier $quoteCopier
     * @param ExpirationCalculator $expirationCalculator
     */
    public function __construct(
        QuoteManagement $quoteManagement,
        CartRepositoryInterface $cartRepository,
        Copier $quoteCopier,
        ExpirationCalculator $expirationCalculator
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->cartRepository = $cartRepository;
        $this->quoteCopier = $quoteCopier;
        $this->expirationCalculator = $expirationCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function changeStatus($quoteId, $status)
    {
        return $this->quoteManagement->changeStatus($quoteId, $status, true, true);
    }

    /**
     * @inheritdoc
     */
    public function createQuote($cartId, $quote)
    {
        $cart = $this->cartRepository->get($cartId);
        $this->quoteManagement->validateCartItemsBeforeBuy($cart);
        $quote
            ->setCustomerId($cart->getCustomer()->getId())
            ->setStatus(Status::PENDING_SELLER_REVIEW)
            ->setIsSeller(true);
        return $this->quoteManagement->createQuote($cart, $quote);
    }

    /**
     * @inheritdoc
     */
    public function updateQuote($quote)
    {
        $cart = $this->cartRepository->get($quote->getCartId());
        $this->quoteManagement->validateCartItemsBeforeBuy($cart);
        $quote->setIsSeller(true);

        return $this->quoteManagement->updateQuote($cart, $quote, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCartByQuote($quote)
    {
        return $this->quoteManagement->getCartByQuote($quote, true);
    }

    /**
     * {@inheritdoc}
     */
    public function copyQuote($quote)
    {
        $copiedCart = $this->quoteCopier->copyCart($quote->getCart());
        $copiedCart->setAwCtqSellerId($quote->getSellerId());
        $copiedCart->setIsActive(false);
        $copiedCart->unsetData(CtqCartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID);
        $this->cartRepository->save($copiedCart);

        $copiedQuote = $this->quoteCopier->copyQuote($quote);
        $copiedQuote
            ->setStatus(Status::PENDING_SELLER_REVIEW)
            ->setIsSeller(true)
            ->setReminderDate(null)
            ->setExpirationDate($this->expirationCalculator->calculateExpirationDate($quote->getStoreId()))
            ->setReminderStatus(ExpirationReminderStatus::READY_TO_BE_SENT);

        return $this->quoteManagement->createQuote($copiedCart, $copiedQuote);
    }

    /**
     * @inheritdoc
     */
    public function sell($quote)
    {
        $cart = $this->cartRepository->get($quote->getCartId());
        $cart->setIsActive(false);
        $cart->setAwCtqSellerId($quote->getSellerId());
        $cart->setItems([]);

        $this->quoteManagement->validateCartItemsBeforeBuy($cart);

        $this->cartRepository->save($cart);
    }
}
