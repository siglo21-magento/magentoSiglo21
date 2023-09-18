<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface QuoteInterface
 * @api
 */
interface QuoteInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const STORE_ID = 'store_id';
    const CART_ID = 'cart_id';
    const ORDER_ID = 'order_id';
    const CUSTOMER_ID = 'customer_id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const LAST_UPDATED_AT = 'last_updated_at';
    const STATUS = 'status';
    const CC_EMAIL_RECEIVER = 'cc_email_receiver';
    const REMINDER_DATE = 'reminder_date';
    const EXPIRATION_DATE = 'expiration_date';
    const REMINDER_STATUS = 'reminder_status';
    const CART = 'cart';
    const NEGOTIATED_DISCOUNT_TYPE = 'negotiated_discount_type';
    const NEGOTIATED_DISCOUNT_VALUE = 'negotiated_discount_value';
    const BASE_QUOTE_TOTAL = 'base_quote_total';
    const QUOTE_TOTAL = 'quote_total';
    const BASE_QUOTE_TOTAL_NEGOTIATED = 'base_quote_total_negotiated';
    const QUOTE_TOTAL_NEGOTIATED = 'quote_total_negotiated';
    const SELLER_ID = 'seller_id';
    const COMMENT = 'comment';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get cart id
     *
     * @return int
     */
    public function getCartId();

    /**
     * Set cart id
     *
     * @param int $cartId
     * @return $this
     */
    public function setCartId($cartId);

    /**
     * Get order id
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param int|null $order
     * @return $this
     */
    public function setOrderId($order);

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get created at date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get last updated at date
     *
     * @return string
     */
    public function getLastUpdatedAt();

    /**
     * Set last updated at date
     *
     * @param string $lastUpdatedAt
     * @return $this
     */
    public function setLastUpdatedAt($lastUpdatedAt);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get cart
     *
     * @return \Aheadworks\Ctq\Api\Data\QuoteCartInterface
     */
    public function getCart();

    /**
     * Set cart
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteCartInterface $cart
     * @return $this
     */
    public function setCart($cart);

    /**
     * Get cc email receiver
     *
     * @return string
     */
    public function getCcEmailReceiver();

    /**
     * Set cc email receiver
     *
     * @param string $ccEmailReceiver
     * @return $this
     */
    public function setCcEmailReceiver($ccEmailReceiver);

    /**
     * Get reminder date
     *
     * @return string
     */
    public function getReminderDate();

    /**
     * Set reminder date
     *
     * @param string $reminderDate
     * @return $this
     */
    public function setReminderDate($reminderDate);

    /**
     * Get expiration date
     *
     * @return string
     */
    public function getExpirationDate();

    /**
     * Set expiration date
     *
     * @param string $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate);

    /**
     * Get reminder status
     *
     * @return string
     */
    public function getReminderStatus();

    /**
     * Set reminder status
     *
     * @param string $reminderStatus
     * @return $this
     */
    public function setReminderStatus($reminderStatus);

    /**
     * Get negotiated discount type
     *
     * @return string
     */
    public function getNegotiatedDiscountType();

    /**
     * Set negotiated discount type
     *
     * @param string $discountType
     * @return $this
     */
    public function setNegotiatedDiscountType($discountType);

    /**
     * Get negotiated discount value
     *
     * @return float
     */
    public function getNegotiatedDiscountValue();

    /**
     * Set negotiated discount value
     *
     * @param float $discountValue
     * @return $this
     */
    public function setNegotiatedDiscountValue($discountValue);

    /**
     * Get base quote total
     *
     * @return float
     */
    public function getBaseQuoteTotal();

    /**
     * Set base quote total
     *
     * @param float $baseQuoteTotal
     * @return $this
     */
    public function setBaseQuoteTotal($baseQuoteTotal);

    /**
     * Get quote total
     *
     * @return float
     */
    public function getQuoteTotal();

    /**
     * Set quote total
     *
     * @param float $quoteTotal
     * @return $this
     */
    public function setQuoteTotal($quoteTotal);

    /**
     * Get base quote total
     *
     * @return float
     */
    public function getBaseQuoteTotalNegotiated();

    /**
     * Set base quote total
     *
     * @param float $baseQuoteTotalNegotiated
     * @return $this
     */
    public function setBaseQuoteTotalNegotiated($baseQuoteTotalNegotiated);

    /**
     * Get quote total negotiated
     *
     * @return float
     */
    public function getQuoteTotalNegotiated();

    /**
     * Set quote total negotiated
     *
     * @param float $quoteTotalNegotiated
     * @return $this
     */
    public function setQuoteTotalNegotiated($quoteTotalNegotiated);

    /**
     * Get seller id
     *
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller id
     *
     * @param int|null $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);

    /**
     * Get comment
     *
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface|null
     */
    public function getComment();

    /**
     * Set comment
     * Please set comment if you want save quote object with comment
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface|null $comment
     * @return $this
     */
    public function setComment($comment);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ctq\Api\Data\QuoteExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\QuoteExtensionInterface $extensionAttributes
    );
}
