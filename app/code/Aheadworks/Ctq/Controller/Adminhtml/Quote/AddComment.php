<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\Page;

/**
 * Class AddComment
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class AddComment extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @param Context $context
     * @param CommentInterfaceFactory $commentFactory
     * @param CommentManagementInterface $commentManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        CommentInterfaceFactory $commentFactory,
        CommentManagementInterface $commentManagement,
        QuoteRepositoryInterface $quoteRepository,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->commentFactory = $commentFactory;
        $this->quoteRepository = $quoteRepository;
        $this->commentManagement = $commentManagement;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = ['error' => true, 'message' => __('Invalid response data.')];

        if ($data = $this->getRequest()->getPostValue()) {
            $quoteId = $this->getRequest()->getParam('quote_id');
            if ($quoteId) {
                try {
                    /** @var CommentInterface $commentObject */
                    $commentObject = $this->commentFactory->create();
                    $this->dataObjectHelper->populateWithArray(
                        $commentObject,
                        $data,
                        CommentInterface::class
                    );

                    $this->validate($commentObject);

                    $quote = $this->quoteRepository->get($quoteId);
                    $commentObject
                        ->setOwnerId($quote->getSellerId())
                        ->setOwnerType(Owner::SELLER);
                    $this->commentManagement->addComment($commentObject);

                    /** @var Page $response */
                    $response = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                    $layout = $response->addHandle('aw_ctq_quote_edit_comments_and_history_layout')->getLayout();
                    $commentHtml = $layout->getBlock('aw_ctq.customer.quote.comment.list')->toHtml();

                    $result = ['error' => false, 'content' => $commentHtml];
                } catch (LocalizedException $e) {
                    $result = ['error' => true, 'message' => __($e->getMessage())];
                } catch (\Exception $e) {
                    $result = ['error' => true, 'message' => __($e->getMessage())];
                }
            }
        }

        return $resultJson->setData($result);
    }

    /**
     * Validate form
     *
     * @param CommentInterface $commentObject
     * @throws LocalizedException
     */
    private function validate(CommentInterface $commentObject)
    {
        if (!strlen($commentObject->getComment()) && !$commentObject->getAttachments()) {
            throw new LocalizedException(__('Please, add comment or(and) attach files.'));
        }
    }
}
