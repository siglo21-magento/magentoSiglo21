<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\QuoteListManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class QuoteListService
 * @package Aheadworks\Ctq\Model\Service
 */
class QuoteListService implements QuoteListManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param CartManagementInterface $cartManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CartManagementInterface $cartManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->cartManagement = $cartManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function createQuoteList()
    {
        return $this->createQuote();
    }

    /**
     * {@inheritDoc}
     */
    public function createQuoteListForCustomer($customerId)
    {
        $customerId = $this->customerRepository->getById($customerId)->getId();
        $quoteId = $this->createQuote();
        $quote = $this->quoteRepository->get($quoteId);
        $quote->setData(CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID, $customerId);
        $quote->setData(OrderInterface::CUSTOMER_EMAIL, $this->getCustomerEmail($customerId));
        $this->quoteRepository->save($quote);

        return $quoteId;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteList($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $quote->setCustomerId($quote->getData(CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID) ?: $quote->getCustomerId());

        return $quote;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteListForCustomer($customerId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID, $customerId)
            ->addFilter(\Magento\Quote\Api\Data\CartInterface::KEY_IS_ACTIVE, 1)
            ->setPageSize(1)
            ->create();

        $items = $this->quoteRepository->getList($searchCriteria)->getItems();

        return empty($items) ? null : reset($items);
    }

    /**
     * {@inheritDoc}
     */
    public function mergeQuoteLists($customerQuote, $quoteToMerge)
    {
        $this->quoteRepository->save(
            $customerQuote->merge($quoteToMerge)->collectTotals()
        );
    }

    /**
     * Create empty quote if not exist
     *
     * @return int
     * @throws CouldNotSaveException
     */
    private function createQuote()
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $quoteId = $this->cartManagement->createEmptyCart();

        $quoteIdMask->setQuoteId($quoteId)->save();

        return $quoteId;
    }

    /**
     * Get customer Email by Id
     *
     * @param int $customerId
     * @return string|null
     * @throws LocalizedException
     */
    private function getCustomerEmail($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $customer->getEmail();
    }
}
