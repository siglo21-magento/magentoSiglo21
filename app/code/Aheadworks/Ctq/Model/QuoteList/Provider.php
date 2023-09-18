<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\QuoteList;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ctq\Api\QuoteListManagementInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class Provider
 * @package Aheadworks\Ctq\Model\QuoteList
 */
class Provider
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var QuoteListManagementInterface
     */
    private $quoteListManagement;

    /**
     * @var CartInterface|Quote
     */
    private $quote;

    /**
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param QuoteListManagementInterface $quoteListManagement
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        QuoteListManagementInterface $quoteListManagement
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->quoteListManagement = $quoteListManagement;
    }

    /**
     * Get CTQ Quote ID
     *
     * @return string|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuoteId()
    {
        return $this->checkoutSession->getAwCtqQuoteListId() ?: $this->getQuote()->getId();
    }

    /**
     * Get CTQ quote list instance
     *
     * @return CartInterface|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuote()
    {
        if ($this->quote === null) {
            $customerId = $this->customerSession->getCustomerId();
            $quoteId = $customerId ? $this->getQuoteForCustomer($customerId) : null;
            $quoteId = $quoteId ?: $this->checkoutSession->getAwCtqQuoteListId();
            $quoteId = $quoteId ?: $this->quoteListManagement->createQuoteList();
            $quote = $this->quoteListManagement->getQuoteList($quoteId);
            $quote->setCustomerGroupId($this->customerSession->getCustomerGroupId());
            $this->quote = $quote;
            $this->checkoutSession->setAwCtqQuoteListId($quoteId);
        }

        return $this->quote;
    }

    /**
     * Retrieve quote for customer
     *
     * @param int $customerId
     * @return int
     * @throws CouldNotSaveException
     */
    private function getQuoteForCustomer($customerId)
    {
        $quote = $this->quoteListManagement->getQuoteListForCustomer($customerId);
        return $quote ? $quote->getId() : $this->quoteListManagement->createQuoteListForCustomer($customerId);
    }
}
