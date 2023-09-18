<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\State;
use Magento\Checkout\Controller\Cart\UpdateItemOptions as CartUpdateItemOptions;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObjectFactory;

/**
 * Class UpdateItemOptions
 * @package Aheadworks\Ctq\Controller\QuoteList
 */
class UpdateItemOptions extends CartUpdateItemOptions
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param State $state
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        CustomerCart $cart,
        State $state,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);
        $this->state = $state;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return $this->state->emulateQuote([$this, 'updateQuoteListItem']);
    }

    /**
     * Update quote item list item
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function updateQuoteListItem()
    {
        $this->validate();
        $id = (int)$this->getRequest()->getParam('id');
        $params = $this->getParams();
        $returnUrl = $this->getRequest()->getParam('return_url');

        try {
            $quoteListItem = $this->cart->updateItem(
                $id,
                $this->dataObjectFactory->create(['data' => $params])
            );

            if (is_string($quoteListItem)) {
                throw new LocalizedException(__($quoteListItem));
            }
            if ($quoteListItem->getHasError()) {
                throw new LocalizedException(__($quoteListItem->getMessage()));
            }

            $this->cart->save();

            if (!$this->cart->getQuote()->getHasError()) {
                $this->messageManager->addSuccessMessage(__(
                    '%1 was updated in your quote list.',
                    $quoteListItem->getProduct()->getName()
                ));
            }

            return $this->_goBack($returnUrl);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t update the item right now.'));

            return $this->_goBack();
        }
    }

    /**
     * Get item options
     *
     * @return array
     */
    private function getParams()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = [];
        }
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

        $id = (int)$this->getRequest()->getParam('id');
        $awCtqQuoteItem = $this->cart->getQuote()->getItemById($id);
        if (!$awCtqQuoteItem) {
            throw new LocalizedException(
                __("The quote item isn't found. Verify the item and try again.")
            );
        }
    }
}
