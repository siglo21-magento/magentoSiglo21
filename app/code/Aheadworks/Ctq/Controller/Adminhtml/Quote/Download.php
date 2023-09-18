<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aheadworks\Ctq\Api\CommentManagementInterface;
use Magento\Backend\App\Action as BackendAction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Aheadworks\Ctq\Model\Attachment\File\Downloader as FileDownloader;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;

/**
 * Class Download
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Download extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * {@inheritdoc}
     */
    protected $_publicActions = ['download'];

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param Context $context
     * @param CommentManagementInterface $commentManagement
     * @param FileDownloader $fileDownloader
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        CommentManagementInterface $commentManagement,
        FileDownloader $fileDownloader,
        QuoteRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);
        $this->commentManagement = $commentManagement;
        $this->fileDownloader = $fileDownloader;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $quoteId = $this->getRequest()->getParam('quote_id');
        try {
            $attachment = $this->commentManagement->getAttachment(
                $this->getRequest()->getParam('file'),
                $this->getRequest()->getParam('comment_id'),
                $quoteId
            );
            return $this->fileDownloader->download($attachment);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
