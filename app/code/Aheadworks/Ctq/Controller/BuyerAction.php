<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class BuyerAction
 * @package Aheadworks\Ctq\Controller
 */
abstract class BuyerAction extends Action
{
    /**
     * Check if quote belongs to customer
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = false;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BuyerQuoteManagementInterface
     */
    protected $buyerQuoteManagement;

    /**
     * @var BuyerPermissionManagementInterface
     */
    protected $buyerPermissionManagement;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
        $this->buyerPermissionManagement = $buyerPermissionManagement;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        } elseif (static::IS_QUOTE_BELONGS_TO_CUSTOMER
            && !$this->isQuoteBelongsToCustomer()
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    /**
     * Check that quote edited at the same store as requested
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    protected function isQuoteCanBeEdited()
    {
        $result = true;
        $currentStoreId = $this->storeManager->getStore()->getStoreId();

        if ($currentStoreId != $this->getQuote()->getStoreId()) {
            $this->messageManager->addErrorMessage(
                __(
                    'Quote "%1" can\'t be edited at this store view.',
                    $this->getQuote()->getName()
                )
            );
            $result = false;
        }

        return $result;
    }

    /**
     * Retrieve cart
     *
     * @return CartInterface
     */
    protected function getCart()
    {
        $cart = null;
        try {
            $quoteId = (int)$this->getRequest()->getParam('quote_id');
            if ($quoteId) {
                $storeId = $this->storeManager->getStore()->getId();
                /** @var CartInterface $cart */
                $cart = $this->buyerQuoteManagement->getCartByQuote($quoteId, $storeId);
            }
        } catch (LocalizedException $e) {
        }

        return $cart;
    }

    /**
     * Retrieve quote
     *
     * @return QuoteInterface
     * @throws NotFoundException
     */
    protected function getQuote()
    {
        try {
            $quoteId = (int)$this->getRequest()->getParam('quote_id');
            $requestEntity = $this->quoteRepository->get($quoteId);
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $requestEntity;
    }

    /**
     * Check if quote belongs to current customer
     *
     * @return bool
     * @throws NotFoundException
     */
    protected function isQuoteBelongsToCustomer()
    {
        $quote = $this->getQuote();
        if ($quote->getId()
            && $quote->getCustomerId() == $this->customerSession->getCustomerId()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Set back link
     *
     * @param $resultPage
     * @param $backUrl
     * @return void
     */
    protected function setBackLink($resultPage, $backUrl = null)
    {
        $backUrl = $backUrl ? : $this->_redirect->getRefererUrl();
        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($backUrl);
        }
    }
}
