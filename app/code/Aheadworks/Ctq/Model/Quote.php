<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ctq\Model\ResourceModel\Quote as ResourceQuote;
use Aheadworks\Ctq\Model\Quote\EntityProcessor;
use Aheadworks\Ctq\Model\Quote\Validator;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\Ctq\Api\CommentManagementInterface;

/**
 * Class Quote
 * @package Aheadworks\Ctq\Model
 */
class Quote extends AbstractModel implements QuoteInterface
{
    /**
     * @var EntityProcessor
     */
    private $processor;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var HistoryManagement
     */
    private $historyManagement;

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param EntityProcessor $processor
     * @param Validator $validator
     * @param CommentManagementInterface $commentManagement
     * @param HistoryManagement $historyManagement
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EntityProcessor $processor,
        Validator $validator,
        CommentManagementInterface $commentManagement,
        HistoryManagement $historyManagement,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->processor = $processor;
        $this->validator = $validator;
        $this->commentManagement = $commentManagement;
        $this->historyManagement = $historyManagement;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceQuote::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCartId()
    {
        return $this->getData(self::CART_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartId($cartId)
    {
        return $this->setData(self::CART_ID, $cartId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastUpdatedAt()
    {
        return $this->getData(self::LAST_UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastUpdatedAt($lastUpdatedAt)
    {
        return $this->setData(self::LAST_UPDATED_AT, $lastUpdatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        return $this->getData(self::CART);
    }

    /**
     * {@inheritdoc}
     */
    public function setCart($cart)
    {
        return $this->setData(self::CART, $cart);
    }

    /**
     * {@inheritdoc}
     */
    public function getCcEmailReceiver()
    {
        return $this->getData(self::CC_EMAIL_RECEIVER);
    }

    /**
     * {@inheritdoc}
     */
    public function setCcEmailReceiver($ccEmailReceiver)
    {
        return $this->setData(self::CC_EMAIL_RECEIVER, $ccEmailReceiver);
    }

    /**
     * {@inheritdoc}
     */
    public function getReminderDate()
    {
        return $this->getData(self::REMINDER_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setReminderDate($reminderDate)
    {
        return $this->setData(self::REMINDER_DATE, $reminderDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationDate()
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationDate($expirationDate)
    {
        return $this->setData(self::EXPIRATION_DATE, $expirationDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getReminderStatus()
    {
        return $this->getData(self::REMINDER_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setReminderStatus($reminderStatus)
    {
        return $this->setData(self::REMINDER_STATUS, $reminderStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function getNegotiatedDiscountType()
    {
        return $this->getData(self::NEGOTIATED_DISCOUNT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setNegotiatedDiscountType($discountType)
    {
        return $this->setData(self::NEGOTIATED_DISCOUNT_TYPE, $discountType);
    }

    /**
     * {@inheritdoc}
     */
    public function getNegotiatedDiscountValue()
    {
        return $this->getData(self::NEGOTIATED_DISCOUNT_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setNegotiatedDiscountValue($discountValue)
    {
        return $this->setData(self::NEGOTIATED_DISCOUNT_VALUE, $discountValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseQuoteTotal()
    {
        return $this->getData(self::BASE_QUOTE_TOTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseQuoteTotal($baseQuoteTotal)
    {
        return $this->setData(self::BASE_QUOTE_TOTAL, $baseQuoteTotal);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteTotal()
    {
        return $this->getData(self::QUOTE_TOTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteTotal($quoteTotal)
    {
        return $this->setData(self::QUOTE_TOTAL, $quoteTotal);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseQuoteTotalNegotiated()
    {
        return $this->getData(self::BASE_QUOTE_TOTAL_NEGOTIATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseQuoteTotalNegotiated($baseQuoteTotalNegotiated)
    {
        return $this->setData(self::BASE_QUOTE_TOTAL_NEGOTIATED, $baseQuoteTotalNegotiated);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteTotalNegotiated()
    {
        return $this->getData(self::QUOTE_TOTAL_NEGOTIATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteTotalNegotiated($quoteTotalNegotiated)
    {
        return $this->setData(self::QUOTE_TOTAL_NEGOTIATED, $quoteTotalNegotiated);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\QuoteExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->processor->prepareDataBeforeSave($this);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $this->processor->prepareDataAfterLoad($this);
        $this->addComment($this);
        $this->historyManagement->addQuoteToHistory($this);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoad()
    {
        $this->processor->prepareDataAfterLoad($this);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * Add comment
     *
     * @param QuoteInterface $quote
     * @return void
     * @throws LocalizedException
     */
    protected function addComment($quote)
    {
        if ($comment = $quote->getComment()) {
            $isSeller = $quote->getIsSeller();
            $comment
                ->setQuoteId($quote->getId())
                ->setOwnerId($isSeller ? $quote->getSellerId() : $quote->getCustomerId())
                ->setOwnerType($isSeller ? Owner::SELLER : Owner::BUYER)
                ->setSkipHistory(true);
            $comment = $this->commentManagement->addComment($comment);
            $quote->setComment($comment);
        }
    }
}
