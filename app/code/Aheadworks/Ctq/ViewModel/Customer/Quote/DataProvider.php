<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Customer\Quote;

use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DataProvider
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote
 */
class DataProvider implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $quoteIdParamName;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $quoteRepository
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param QuoteInterfaceFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param string $quoteIdParamName
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $quoteRepository,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        QuoteInterfaceFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        $quoteIdParamName = 'quote_id'
    ) {
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->quoteIdParamName = $quoteIdParamName;
    }

    /**
     * Retrieve active quote
     *
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote()
    {
        $quoteId = $this->request->getParam($this->quoteIdParamName);
        if (!$quoteId) {
            $quoteId = $this->request->getParam('quote_id');
        }
        if ($quoteId) {
            $storeId = $this->storeManager->getStore()->getId();
            $this->buyerQuoteManagement->getCartByQuote($quoteId, $storeId);
            $quote = $this->quoteRepository->get($quoteId);
        } else {
            $quote = $this->quoteFactory->create();
        }

        return $quote;
    }

    /**
     * Retrieve active quote id
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteId()
    {
        $quoteId = $this->request->getParam($this->quoteIdParamName);
        if (!$quoteId) {
            $quoteId = $this->request->getParam('quote_id');
        }

        return $quoteId ? $this->quoteRepository->get($quoteId)->getId() : $quoteId;
    }

    /**
     * Retrieve cart
     *
     * @return CartInterface|Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCart()
    {
        $quote = $this->getQuote();
        $storeId = $this->storeManager->getStore()->getId();
        /** @var CartInterface|Quote $cart */
        $cart = $this->buyerQuoteManagement->getCartByQuote($quote, $storeId);

        return $cart;
    }
}
