<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TransactionInterface
 * @api
 */
interface TransactionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const SUMMARY_ID = 'summary_id';
    const COMPANY_ID = 'company_id';
    const CREATED_AT = 'created_at';
    const ACTION = 'action';
    const AMOUNT = 'amount';
    const CREDIT_LIMIT = 'credit_limit';
    const CREDIT_BALANCE = 'credit_balance';
    const CREDIT_AVAILABLE = 'credit_available';
    const CREDIT_CURRENCY = 'credit_currency';
    const ACTION_CURRENCY = 'action_currency';
    const RATE_TO_CREDIT_CURRENCY = 'rate_to_credit_currency';
    const RATE_TO_ACTION_CURRENCY = 'rate_to_action_currency';
    const PO_NUMBER = 'po_number';
    const UPDATED_BY = 'updated_by';
    const COMMENT_TO_CUSTOMER = 'comment_to_customer';
    const COMMENT_TO_CUSTOMER_PLACEHOLDER = 'comment_to_customer_placeholder';
    const COMMENT_TO_ADMIN = 'comment_to_admin';
    const ENTITIES = 'entities';
    /**#@-*/

    /**
     * Set transaction ID
     *
     * @param  int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get transaction ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set summary ID
     *
     * @param int $summaryId
     * @return $this
     */
    public function setSummaryId($summaryId);

    /**
     * Get summary ID
     *
     * @return int
     */
    public function getSummaryId();

    /**
     * Set company ID
     *
     * @param int $companyId
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
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

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
     * @param float $amount
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
     * Set credit limit
     *
     * @param float $creditLimit
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
     * Set credit balance
     *
     * @param float $creditBalance
     * @return SummaryInterface
     */
    public function setCreditBalance($creditBalance);

    /**
     * Get credit balance
     *
     * @return float
     */
    public function getCreditBalance();

    /**
     * Set credit available
     *
     * @param float $creditAvailable
     * @return SummaryInterface
     */
    public function setCreditAvailable($creditAvailable);

    /**
     * Get credit available
     *
     * @return float
     */
    public function getCreditAvailable();

    /**
     * Set credit currency
     *
     * @param string $creditCurrency
     * @return SummaryInterface
     */
    public function setCreditCurrency($creditCurrency);

    /**
     * Get credit currency
     *
     * @return string
     */
    public function getCreditCurrency();

    /**
     * Set action currency
     *
     * @param string $actionCurrency
     * @return SummaryInterface
     */
    public function setActionCurrency($actionCurrency);

    /**
     * Get action currency
     *
     * @return string
     */
    public function getActionCurrency();

    /**
     * Set rate to credit currency
     *
     * @param float $rateToCreditCurrency
     * @return SummaryInterface
     */
    public function setRateToCreditCurrency($rateToCreditCurrency);

    /**
     * Get rate to credit currency
     *
     * @return float
     */
    public function getRateToCreditCurrency();

    /**
     * Set rate to action currency
     *
     * @param float $rateToActionCurrency
     * @return SummaryInterface
     */
    public function setRateToActionCurrency($rateToActionCurrency);

    /**
     * Get rate to action currency
     *
     * @return float
     */
    public function getRateToActionCurrency();

    /**
     * Set po number
     *
     * @param string $poNumber
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
     * Set updated by
     *
     * @param string $updatedBy
     * @return $this
     */
    public function setUpdatedBy($updatedBy);

    /**
     * Get updated by
     *
     * @return string|null
     */
    public function getUpdatedBy();

    /**
     * Set comment to customer
     *
     * @param string $commentToCustomer
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
     * Set comment to customer
     *
     * @param string $commentToCustomerPlaceholder
     * @return $this
     */
    public function setCommentToCustomerPlaceholder($commentToCustomerPlaceholder);

    /**
     * Get comment to customer placeholder
     *
     * @return string|null
     */
    public function getCommentToCustomerPlaceholder();

    /**
     * Set comment to admin
     *
     * @param string $commentToAdmin
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
     * Set transaction entities
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface[] $entities
     * @return $this
     */
    public function setEntities($entities);

    /**
     * Get transaction entities
     *
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionEntityInterface[]|null
     */
    public function getEntities();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\TransactionExtensionInterface $extensionAttributes
    );
}
