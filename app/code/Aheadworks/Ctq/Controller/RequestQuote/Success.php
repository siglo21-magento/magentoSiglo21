<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\RequestQuote;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Success
 * @package Aheadworks\Ctq\Controller\RequestQuote
 */
class Success extends BuyerAction
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        PageFactory $resultPageFactory
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $storeManager,
            $buyerQuoteManagement,
            $buyerPermissionManagement,
            $quoteRepository
        );
        $this->checkoutSession = $checkoutSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->isValid()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        $resultPage = $this->resultPageFactory->create();
        $this->checkoutSession
            ->setAwCtqLastQuoteId(null)
            ->setAwCtqQuoteListId(null);

        return $resultPage;
    }

    /**
     * Check if is valid
     *
     * @return bool
     */
    private function isValid()
    {
        return $this->checkoutSession->getAwCtqLastQuoteId() && $this->checkoutSession->getAwCtqLastRealQuoteId();
    }
}
