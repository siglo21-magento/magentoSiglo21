<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater\ShippingChecker;

/**
 * Class UpdateProcessor
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin
 */
class UpdateProcessor
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var QuoteUpdater
     */
    private $quoteUpdater;

    /**
     * @var ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * @var ShippingChecker
     */
    private $shippingChecker;

    /**
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param QuoteSession $quoteSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param QuoteUpdater $quoteUpdater
     * @param ObjectManagerInterface $objectManager
     * @param ShippingChecker $shippingChecker
     */
    public function __construct(
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        QuoteSession $quoteSession,
        CustomerRepositoryInterface $customerRepository,
        QuoteUpdater $quoteUpdater,
        ObjectManagerInterface $objectManager,
        ShippingChecker $shippingChecker
    ) {
        $productHelper->setSkipSaleableCheck(true);
        $this->escaper = $escaper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->quoteSession = $quoteSession;
        $this->customerRepository = $customerRepository;
        $this->quoteUpdater = $quoteUpdater;
        $this->_objectManager = $objectManager;
        $this->shippingChecker = $shippingChecker;
    }

    /**
     * Retrieve session object
     *
     * @return \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_objectManager->get(\Aheadworks\Ctq\Model\Quote\Admin\Session\Quote::class);
    }

    /**
     * Retrieve quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve order create model
     *
     * @return \Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater
     */
    protected function _getOrderCreateModel()
    {
        return $this->_objectManager->get(\Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater::class);
    }

    /**
     * Retrieve gift message save model
     *
     * @return \Magento\GiftMessage\Model\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_objectManager->get(\Magento\GiftMessage\Model\Save::class);
    }

    /**
     * Process request
     *
     * @param RequestInterface $request
     * @param string|null $action
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function processRequest(RequestInterface $request, $action = null)
    {
        $this->setRequest($request);
        $this->updateSession();
        $this->updateData($action);

        return $this;
    }

    /**
     * Update session data
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function updateSession()
    {
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        if ($customerId) {
            if (!$this->quoteSession->getCustomerId()) {
                $this->quoteSession->setCustomerId($customerId);
            }

            $storeId = (int) $this->getRequest()->getParam('store_id');
            if (!$this->quoteSession->getStoreId()) {
                if ($storeId) {
                    $this->quoteSession->setStoreId($storeId);
                } else {
                    $customer = $this->customerRepository->getById($customerId);
                    $this->quoteSession->setStoreId($customer->getStoreId());
                }
            }
        }

        return $this;
    }

    /**
     * Update quote data
     *
     * @param string|null $action
     * @return $this
     * @throws LocalizedException
     */
    protected function updateData($action = null)
    {
        $this->quoteUpdater->getQuote()->setAwCtqIsNotRequireValidation(true);

        $this->quoteUpdater->setAwCtqQuoteToCart($this->getRequest()->getParams());

        if ($data = $this->getRequest()->getPost('shipping')) {
            if ($action == 'save') {
                $this->shippingChecker->processData($data, $this->quoteUpdater->getQuote());
                $this->quoteUpdater->importShippingInformation($data);
            } else {
                $this->quoteUpdater->importShippingInformation($data);
            }
        }

        /**
         * Initialize catalog rule data
         */
        $this->quoteUpdater->initRuleData();

        /**
         * init first billing address, need for virtual products
         */
        $this->quoteUpdater->getBillingAddress();

        if (!$this->quoteUpdater->getQuote()->isVirtual()) {
            $this->quoteUpdater->setShippingAsBilling(false);
        }

        /**
         * Change shipping address flag
         */
        if (!$this->quoteUpdater->getQuote()->isVirtual() && $this->getRequest()->getPost('reset_shipping')
        ) {
            $this->quoteUpdater->resetShippingMethod(true);
        }

        /**
         * Reset items calculate flag
         */
        if ($this->getRequest()->getPost('reset_calculation')) {
            $this->quoteUpdater->resetItems();
        }

        /**
         * Collecting shipping rates
         */
        if (!$this->quoteUpdater->getQuote()->isVirtual() && $this->getRequest()->getPost('collect_shipping_rates')
        ) {
            $this->quoteUpdater->collectShippingRates();
        }

        /**
         * Apply mass changes from sidebar
         */
        if ($data = $this->getRequest()->getPost('sidebar')) {
            $this->quoteUpdater->applySidebarData($data);
        }

        /**
         * Adding product to quote from shopping cart, wishlist etc.
         */
        if ($productId = (int)$this->getRequest()->getPost('add_product')) {
            $this->quoteUpdater->addProduct($productId, $this->getRequest()->getPostValue());
        }

        /**
         * Adding products to quote from special grid
         */
        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items') && !($action == 'save')
        ) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->quoteUpdater->addProducts($items);
        }

        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->_processFiles($items);
            $this->quoteUpdater->updateQuoteItems($items);
        }

        /**
         * Remove quote item
         */
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->quoteUpdater->removeItem($removeItemId, $removeFrom);
            $this->quoteUpdater->recollectCart();
        }

        /**
         * Move quote item
         */
        $moveItemId = (int)$this->getRequest()->getPost('move_item');
        $moveTo = (string)$this->getRequest()->getPost('to');
        $moveQty = (int)$this->getRequest()->getPost('qty');
        if ($moveItemId && $moveTo) {
            $this->quoteUpdater->moveQuoteItem($moveItemId, $moveTo, $moveQty);
        }

        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->quoteUpdater->getQuote()->getPayment()->addData($paymentData);
        }

        $this->quoteUpdater->saveQuote();

        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->quoteUpdater->getQuote()->getPayment()->addData($paymentData);
        }

        /**
         * Saving of giftmessages
         */
        $giftmessages = $this->getRequest()->getPost('giftmessage');
        if ($giftmessages) {
            $this->_getGiftmessageSaveModel()->setGiftmessages($giftmessages)->saveAllInQuote();
        }

        /**
         * Importing gift message allow items from specific product grid
         */
        if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromProducts(
                $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonDecode($data)
            );
        }

        /**
         * Importing gift message allow items on update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromItems($items);
        }

        $data = $this->getRequest()->getPost('order');
        $couponCode = '';
        if (isset($data) && isset($data['coupon']['code'])) {
            $couponCode = trim($data['coupon']['code']);
        }

        return $this;
    }

    /**
     * Process buyRequest file options of items
     *
     * @param array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        /* @var $productHelper \Magento\Catalog\Helper\Product */
        $productHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Product::class);
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\DataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }

    /**
     * Set request
     *
     * @param RequestInterface $request
     * @return $this
     */
    private function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get request
     *
     * @return RequestInterface
     */
    private function getRequest()
    {
        return $this->request;
    }
}
