<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TransactionParametersInterface
 */
interface TransactionParametersInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const CUSTOMER_ID = 'customer_id';
    const ACTION = 'action';
    const AMOUNT = 'amount';
    const AMOUNT_CURRENCY = 'amount_currency';
    const USED_CURRENCY = 'used_currency';
    const CREDIT_LIMIT = 'credit_limit';
    const IS_CUSTOM_CREDIT_LIMIT = 'is_custom_credit_limit';
    const IS_ALLOWED_TO_EXCEED_LIMIT = 'is_allowed_to_exceed_limit';
    const PO_NUMBER = 'po_number';
    const COMMENT_TO_CUSTOMER = 'comment_to_customer';
    const COMMENT_TO_ADMIN = 'comment_to_admin';
    const ORDER_ENTITY = 'order_entity';
    const CREDITMEMO_ENTITY = 'creditmemo_entity';
    const COMPANY_ID = 'company_id';
    /**#@-*/

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set action
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action);

    /**
     * Get action
     *
     * @return string
     */
    public function getAction();

    /**
     * Set amount
     *
     * @param float|null $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get amount
     *
     * @return float|null
     */
    public function getAmount();

    /**
     * Set amount currency
     *
     * @param float|null $amountCurrency
     * @return $this
     */
    public function setAmountCurrency($amountCurrency);

    /**
     * Get amount currency
     *
     * @return float|null
     */
    public function getAmountCurrency();

    /**
     * Set used currency
     *
     * @param float|null $usedCurrency
     * @return $this
     */
    public function setUsedCurrency($usedCurrency);

    /**
     * Get usd currency
     *
     * @return float|null
     */
    public function getUsedCurrency();

    /**
     * Set credit limit
     *
     * @param float|null $creditLimit
     * @return SummaryInterface
     */
    public function setCreditLimit($creditLimit);

    /**
     * Get credit limit
     *
     * @return float
     */
    public function getCreditLimit();

    /**
     * Set is custom credit limit
     *
     * @param bool|null $isCustomCreditLimit
     * @return SummaryInterface
     */
    public function setIsCustomCreditLimit($isCustomCreditLimit);

    /**
     * Get is custom credit limit
     *
     * @return bool|null
     */
    public function getIsCustomCreditLimit();

    /**
     * Set if limit can be exceeded
     *
     * @param bool $isAllowedToExceedLimit
     * @return SummaryInterface
     */
    public function setIsAllowedToExceedLimit($isAllowedToExceedLimit);

    /**
     * Is allowed to exceed limit
     *
     * @return bool|null
     */
    public function isAllowedToExceedLimit();

    /**
     * Set po number
     *
     * @param string|null $poNumber
     * @return $this
     */
    public function setPoNumber($poNumber);

    /**
     * Get po number
     *
     * @return string|null
     */
    public function getPoNumber();

    /**
     * Set comment to customer
     *
     * @param string|null $commentToCustomer
     * @return $this
     */
    public function setCommentToCustomer($commentToCustomer);

    /**
     * Get comment to customer
     *
     * @return string|null
     */
    public function getCommentToCustomer();

    /**
     * Set comment to admin
     *
     * @param string|null $commentToAdmin
     * @return $this
     */
    public function setCommentToAdmin($commentToAdmin);

    /**
     * Get comment to admin
     *
     * @return string|null
     */
    public function getCommentToAdmin();

    /**
     * Set order entity
     *
     * @param \Magento\Sales\Api\Data\OrderInterface|null $order
     * @return $this
     */
    public function setOrderEntity($order);

    /**
     * Get order entity
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    public function getOrderEntity();

    /**
     * Set creditmemo entity
     *
     * @param \Magento\Sales\Api\Data\CreditmemoInterface|null $creditmemo
     * @return $this
     */
    public function setCreditmemoEntity($creditmemo);

    /**
     * Get creditmemo entity
     *
     * @return \Magento\Sales\Api\Data\CreditmemoInterface|null
     */
    public function getCreditmemoEntity();

    /**
     * Set company ID
     *
     * @param int|null $companyId
     * @return $this
     */
    public function setCompanyId($companyId);

    /**
     * Get company ID
     *
     * @return int|null
     */
    public function getCompanyId();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionParametersExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionParametersExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\TransactionParametersExtensionInterface $extensionAttributes
    );
}
