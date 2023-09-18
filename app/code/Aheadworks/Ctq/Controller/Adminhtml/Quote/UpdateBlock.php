<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Aheadworks\Ctq\Model\Quote\Admin\UpdateProcessor;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Reloader as QuoteReloader;
use Magento\Backend\App\Action as BackendAction;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\Result\Raw as ResultRaw;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdateBlock
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class UpdateBlock extends BackendAction
{
    /**
     * Update block layout handle part
     */
    const UPDATE_BLOCK_LAYOUT_HANDLE_PART = 'aw_ctq_quote_edit_update_block_';

    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * @var UpdateProcessor
     */
    private $updateProcessor;

    /**
     * @var QuoteReloader
     */
    private $quoteReloader;

    /**
     * @param Context $context
     * @param UpdateProcessor $updateProcessor
     * @param QuoteReloader $quoteReloader
     */
    public function __construct(
        Context $context,
        UpdateProcessor $updateProcessor,
        QuoteReloader $quoteReloader
    ) {
        $this->updateProcessor = $updateProcessor;
        $this->quoteReloader = $quoteReloader;
        parent::__construct($context);
    }

    /**
     * Loading page block
     *
     * @return ResultRedirect|ResultRaw
     */
    public function execute()
    {
        $request = $this->getRequest();
        try {
            $this->updateProcessor->processRequest($request);
        } catch (LocalizedException $e) {
            $this->quoteReloader->reload();
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->quoteReloader->reload();
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($asJson) {
            $resultPage->addHandle(self::UPDATE_BLOCK_LAYOUT_HANDLE_PART . 'json');
        } else {
            $resultPage->addHandle(self::UPDATE_BLOCK_LAYOUT_HANDLE_PART . 'plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $resultPage->addHandle(self::UPDATE_BLOCK_LAYOUT_HANDLE_PART . $block);
            }
        }

        $result = $resultPage->getLayout()->renderElement('content');
        if ($request->getParam('as_js_varname')) {
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setUpdateResult($result);
            /** @var ResultRedirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('aw_ctq/*/showUpdateResult');
        }
        /** @var ResultRaw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        return $resultRaw->setContents($result);
    }
}
