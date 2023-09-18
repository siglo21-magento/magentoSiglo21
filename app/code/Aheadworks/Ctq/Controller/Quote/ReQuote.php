<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Quote;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReQuote
 *
 * @package Aheadworks\Ctq\Controller\Quote
 */
class ReQuote extends ChangeStatus
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $quote = $this->buyerQuoteManagement->copyQuote($this->getQuote());
            return $resultRedirect->setPath('*/*/edit', ['quote_id' => $quote->getId(), '_current' => true]);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while duplicating the quote.')
            );
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
