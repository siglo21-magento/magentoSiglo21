<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Model\Exception\UpdateForbiddenException;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Checkout\Model\Cart;

/**
 * Class Save
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Save extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var RequestQuantityProcessor
     */
    private $quantityProcessor;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param FormKeyValidator $formKeyValidator
     * @param RequestQuantityProcessor $quantityProcessor
     * @param CartRepositoryInterface $cartRepository
     * @param Cart $cart
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository,
        FormKeyValidator $formKeyValidator,
        RequestQuantityProcessor $quantityProcessor,
        CartRepositoryInterface $cartRepository,
        Cart $cart
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $storeManager,
            $buyerQuoteManagement,
            $buyerPermissionManagement,
            $quoteRepository
        );
        $this->formKeyValidator = $formKeyValidator;
        $this->quantityProcessor = $quantityProcessor;
        $this->cartRepository = $cartRepository;
        $this->cart = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $data = $this->getRequest()->getParam('cart');
            if (!$this->isQuoteCanBeEdited()) {
                return $resultRedirect->setPath('*/*/');
            }
            if (is_array($data)) {
                // @todo move to BuyerQuoteManagementInterface
                $cart = $this->cartRepository->get($this->getQuote()->getCartId());
                $cart->setAwCtqIsNotRequireValidation(true);
                $this->cart->setQuote($cart);
                $data = $this->quantityProcessor->process($data);
                $data = $this->cart->suggestItemsQty($data);
                $this->cart->updateItems($data)->save();
            }
            $quoteObject = $this->prepareQuote();
            $this->performSave($quoteObject);
            return $this->redirectTo($resultRedirect);
        } catch (UpdateForbiddenException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $quoteObject = $this->prepareQuote();
            $this->performSave($quoteObject);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while save the quote.')
            );
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * Prepare quote
     *
     * @return QuoteInterface
     * @throws LocalizedException|\Exception
     */
    protected function prepareQuote()
    {
        $quoteObject = $this->getQuote();
        $quoteObject->setStatus(Status::PENDING_SELLER_REVIEW);

        return $quoteObject;
    }

    /**
     * Perform save
     *
     * @param QuoteInterface $quoteObject
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws UpdateForbiddenException
     */
    protected function performSave($quoteObject)
    {
        return $this->buyerQuoteManagement->updateQuote($quoteObject);
    }

    /**
     * Redirect to
     *
     * @param Redirect $resultRedirect
     * @return Redirect
     */
    protected function redirectTo($resultRedirect)
    {
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
