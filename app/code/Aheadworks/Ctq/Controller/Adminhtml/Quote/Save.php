<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Admin\UpdateProcessor;
use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;
use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\QuoteProcessor;

/**
 * Class Save
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Save extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * @var UpdateProcessor
     */
    protected $updateProcessor;

    /**
     * @var PostDataProcessor
     */
    protected $postDataProcessor;

    /**
     * @var SellerQuoteManagementInterface
     */
    protected $sellerQuoteManagement;

    /**
     * @var QuoteSession
     */
    protected $quoteSession;

    /**
     * @var QuoteProcessor
     */
    protected $quoteProcessor;

    /**
     * @param Context $context
     * @param UpdateProcessor $updateProcessor
     * @param PostDataProcessor $postDataProcessor
     * @param SellerQuoteManagementInterface $sellerQuoteManagement
     * @param QuoteSession $quoteSession
     * @param QuoteProcessor $quoteProcessor
     */
    public function __construct(
        Context $context,
        UpdateProcessor $updateProcessor,
        PostDataProcessor $postDataProcessor,
        SellerQuoteManagementInterface $sellerQuoteManagement,
        QuoteSession $quoteSession,
        QuoteProcessor $quoteProcessor
    ) {
        parent::__construct($context);
        $this->updateProcessor = $updateProcessor;
        $this->postDataProcessor = $postDataProcessor;
        $this->sellerQuoteManagement = $sellerQuoteManagement;
        $this->quoteSession = $quoteSession;
        $this->quoteProcessor = $quoteProcessor;
    }

    /**
     * Save action
     *
     * @return ResultRedirect
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $postData = $this->getRequest()->getPostValue();
        if ($postData) {
            try {
                $data = $this->postDataProcessor->preparePostData($postData);
                $this->updateProcessor->processRequest($this->getRequest(), 'save');
                $cartId = $this->quoteSession->getQuoteId();
                $quote = $this->prepareQuote($data);
                if ($quote->getId()) {
                    $quote = $this->sellerQuoteManagement->updateQuote($quote);
                } else {
                    $quote = $this->sellerQuoteManagement->createQuote(
                        $cartId,
                        $quote
                    );
                }

                $this->messageManager->addSuccessMessage(__('Quote was saved successfully'));
                return $this->redirectTo($resultRedirect, $quote);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the quote'));
            }
            $quoteId = $data['quote']['quote_id'] ?? null;
            if ($quoteId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $quoteId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare quote
     *
     * @param array $data
     * @return QuoteInterface
     * @throws LocalizedException|\Exception
     */
    protected function prepareQuote($data)
    {
        return $this->quoteProcessor->prepare($data);
    }

    /**
     * Redirect to
     *
     * @param Redirect $resultRedirect
     * @param QuoteInterface $quote
     * @return Redirect
     */
    protected function redirectTo($resultRedirect, $quote)
    {
        return $resultRedirect->setPath('*/*/edit', ['id' => $quote->getId()]);
    }
}
