<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class ReQuote
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class ReQuote extends Save
{
    /**
     * Requote action
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
                $quote = $this->sellerQuoteManagement->updateQuote($this->prepareQuote($data));
                $newQuote = $this->sellerQuoteManagement->copyQuote($quote);
                $this->messageManager->addSuccessMessage(__('Quote was duplicated successfully'));
                return $this->redirectTo($resultRedirect, $newQuote);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while duplicating the quote'));
            }
            $quoteId = $data['quote']['quote_id'] ?? null;
            if ($quoteId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $quoteId, '_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
