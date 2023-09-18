<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Model\Service\SellerQuoteService;

/**
 * Class Edit
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Edit extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * {@inheritdoc}
     */
    protected $_publicActions = ['edit'];

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var SellerQuoteService
     */
    private $sellerQuoteManagement;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param QuoteSession $quoteSession
     * @param QuoteRepositoryInterface $quoteRepository
     * @param SellerQuoteService $sellerQuoteManagement
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        QuoteSession $quoteSession,
        QuoteRepositoryInterface $quoteRepository,
        SellerQuoteService $sellerQuoteManagement
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->quoteSession = $quoteSession;
        $this->quoteRepository = $quoteRepository;
        $this->sellerQuoteManagement = $sellerQuoteManagement;
    }

    /**
     * Index action
     *
     * @return ResultPage|Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $quoteId = $this->_request->getParam('id', null);
        $this->quoteSession->clearStorage();
        if ($quoteId) {
            try {
                $cart = $this->sellerQuoteManagement->getCartByQuote($quoteId);
                if ($cart->getAwCtqQuoteIsChanged()) {
                    $this->messageManager->addNoticeMessage(
                        __('This Quote has been updated for some reasons. All details are in the History Log.')
                    );
                }
                $quote = $this->quoteRepository->get($quoteId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addException($exception, __('This quote doesn\'t exist.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
            $this->quoteSession
                ->setQuote($cart)
                ->setCustomerId($quote->getCustomerId())
                ->setStoreId($quote->getStoreId())
                ->setQuoteId($cart->getId());
        }

        $resultPage->setActiveMenu('Aheadworks_Ctq::quotes');
        $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));
        return $resultPage;
    }
}
