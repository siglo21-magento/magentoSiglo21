<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class AddComment
 * @package Aheadworks\Ctq\Controller\Quote
 */
class AddComment extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CommentInterfaceFactory $commentFactory
     * @param CommentManagementInterface $commentManagement
     * @param FormKeyValidator $formKeyValidator
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository,
        CommentInterfaceFactory $commentFactory,
        CommentManagementInterface $commentManagement,
        FormKeyValidator $formKeyValidator,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $storeManager,
            $buyerQuoteManagement,
            $buyerPermissionManagement,
            $quoteRepository
        );
        $this->commentFactory = $commentFactory;
        $this->commentManagement = $commentManagement;
        $this->formKeyValidator = $formKeyValidator;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                /** @var CommentInterface $commentObject */
                $commentObject = $this->commentFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $commentObject,
                    $data,
                    CommentInterface::class
                );

                $this->validate($commentObject);

                $commentObject
                    ->setOwnerId($this->getQuote()->getCustomerId())
                    ->setOwnerType(Owner::BUYER);
                $this->commentManagement->addComment($commentObject);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * Validate form
     *
     * @param CommentInterface $commentObject
     * @throws LocalizedException
     */
    private function validate(CommentInterface $commentObject)
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(__('Invalid Form Key. Please refresh the page.'));
        }

        if (!$commentObject->getComment() && !$commentObject->getAttachments()) {
            throw new LocalizedException(__('Please, add comment or(and) attach files.'));
        }
    }
}
