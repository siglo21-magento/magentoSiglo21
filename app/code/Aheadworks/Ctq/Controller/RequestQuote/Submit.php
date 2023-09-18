<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\RequestQuote;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Model\QuoteList\State;
use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class RequestQuote
 * @package Aheadworks\Ctq\Controller
 */
class Submit extends BuyerAction
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param CommentInterfaceFactory $commentFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param Checker $checker
     * @param State $state
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        CommentInterfaceFactory $commentFactory,
        DataObjectHelper $dataObjectHelper,
        Checker $checker,
        State $state
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
        $this->commentFactory = $commentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->state = $state;
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            if ($this->checker->isQuoteList()) {
                $quote = $this->state->emulateQuote([$this, 'requestQuote'], [true]);
            } else {
                $quote = $this->requestQuote();
            }

            $this->checkoutSession
                ->setAwCtqLastQuoteId($quote->getId())
                ->setAwCtqLastRealQuoteId($quote->getId());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong when requesting a quote.')
            );
        }

        return $resultRedirect->setPath('*/requestQuote/success');
    }

    /**
     * Request quote
     *
     * @param bool $isQuoteList
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function requestQuote($isQuoteList = false)
    {
        $quoteName = $this->getRequest()->getParam('quote_name');
        $comment = $this->getRequest()->getParam('comment');
        /** @var CommentInterface $commentObject */
        $commentObject = $this->commentFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $commentObject,
            $comment,
            CommentInterface::class
        );
        $commentToAdd = null;
        if ($commentObject->getComment() || $commentObject->getAttachments()) {
            $commentToAdd = $commentObject;
        }

        if ($isQuoteList) {
            return $this->buyerQuoteManagement->requestQuoteList(
                $this->checkoutSession->getQuoteId(),
                $quoteName,
                $commentToAdd
            );
        } else {
            return $this->buyerQuoteManagement->requestQuote(
                $this->checkoutSession->getQuoteId(),
                $quoteName,
                $commentToAdd
            );
        }
    }
}
