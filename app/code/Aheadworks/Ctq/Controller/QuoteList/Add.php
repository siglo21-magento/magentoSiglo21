<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\State;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Checkout\Controller\Cart\Add as CartAdd;
use Magento\Checkout\Model\CartFactory;

/**
 * Class Add
 * @package Aheadworks\Ctq\Controller\QuoteList
 */
class Add extends CartAdd
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var CartFactory
     */
    private $cartFactory;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param FormKeyValidator $formKeyValidator
     * @param Cart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param CartFactory $cartFactory
     * @param State $state
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        FormKeyValidator $formKeyValidator,
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        CartFactory $cartFactory,
        State $state
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
        $this->state = $state;
        $this->cartFactory = $cartFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->validate();
        try {
            $product = $this->_initProduct();
            $this->state->emulateQuote([$this, 'addItemToQuoteList'], [$product]);
            $this->messageManager->addComplexSuccessMessage(
                'addQuoteListSuccessMessage',
                ['product_name' => $product->getName()]
            );
            return $this->goBack();
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            if ($url = $this->_checkoutSession->getRedirectUrl()) {
                return $this->goBack($url);
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add this item.')
            );
        }

        return $this->goBack();
    }

    /**
     * Add item to quote list
     *
     * @param ProductInterface $product
     * @throws LocalizedException
     */
    public function addItemToQuoteList($product)
    {
        /** @var Cart $cart */
        $cart = $this->cartFactory->create();
        $params = $this->getParams();

        $cart->addProduct($product, $params);
        $cart->save();
    }

    /**
     * Get item options
     *
     * @return array
     */
    private function getParams()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['qty'])) {
            $params['qty'] = (int)$params['qty'];
        }

        return $params;
    }

    /**
     * Validate form
     *
     * @throws LocalizedException
     */
    private function validate()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(__('Invalid Form Key. Please refresh the page.'));
        }
    }
}
