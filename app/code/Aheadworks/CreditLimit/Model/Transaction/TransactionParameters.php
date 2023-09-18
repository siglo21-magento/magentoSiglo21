<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction;

use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class TransactionParameters
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
class TransactionParameters extends AbstractExtensibleObject implements TransactionParametersInterface
{
    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return  $this->_get(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return $this->_get(self::ACTION);
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setAmountCurrency($amountCurrency)
    {
        return $this->setData(self::AMOUNT_CURRENCY, $amountCurrency);
    }

    /**
     * @inheritdoc
     */
    public function getAmountCurrency()
    {
        return $this->_get(self::AMOUNT_CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setUsedCurrency($usedCurrency)
    {
        return $this->setData(self::USED_CURRENCY, $usedCurrency);
    }

    /**
     * @inheritdoc
     */
    public function getUsedCurrency()
    {
        return $this->_get(self::USED_CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setCreditLimit($creditLimit)
    {
        return $this->setData(self::CREDIT_LIMIT, $creditLimit);
    }

    /**
     * @inheritdoc
     */
    public function getCreditLimit()
    {
        return $this->_get(self::CREDIT_LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setIsCustomCreditLimit($isCustomCreditLimit)
    {
        return $this->setData(self::IS_CUSTOM_CREDIT_LIMIT, $isCustomCreditLimit);
    }

    /**
     * @inheritdoc
     */
    public function getIsCustomCreditLimit()
    {
        return $this->_get(self::IS_CUSTOM_CREDIT_LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setIsAllowedToExceedLimit($isAllowedToExceedLimit)
    {
        return $this->setData(self::IS_ALLOWED_TO_EXCEED_LIMIT, $isAllowedToExceedLimit);
    }

    /**
     * @inheritdoc
     */
    public function isAllowedToExceedLimit()
    {
        return $this->_get(self::IS_ALLOWED_TO_EXCEED_LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setPoNumber($poNumber)
    {
        return $this->setData(self::PO_NUMBER, $poNumber);
    }

    /**
     * @inheritdoc
     */
    public function getPoNumber()
    {
        return $this->_get(self::PO_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setCommentToCustomer($commentToCustomer)
    {
        return $this->setData(self::COMMENT_TO_CUSTOMER, $commentToCustomer);
    }

    /**
     * @inheritdoc
     */
    public function getCommentToCustomer()
    {
        return $this->_get(self::COMMENT_TO_CUSTOMER);
    }

    /**
     * @inheritdoc
     */
    public function setCommentToAdmin($commentToAdmin)
    {
        return $this->setData(self::COMMENT_TO_ADMIN, $commentToAdmin);
    }

    /**
     * @inheritdoc
     */
    public function getCommentToAdmin()
    {
        return $this->_get(self::COMMENT_TO_ADMIN);
    }

    /**
     * @inheritdoc
     */
    public function setOrderEntity($order)
    {
        return $this->setData(self::ORDER_ENTITY, $order);
    }

    /**
     * @inheritdoc
     */
    public function getOrderEntity()
    {
        return $this->_get(self::ORDER_ENTITY);
    }

    /**
     * @inheritdoc
     */
    public function setCreditmemoEntity($creditmemo)
    {
        return $this->setData(self::CREDITMEMO_ENTITY, $creditmemo);
    }

    /**
     * @inheritdoc
     */
    public function getCreditmemoEntity()
    {
        return $this->_get(self::CREDITMEMO_ENTITY);
    }

    /**
     * @inheritdoc
     */
    public function setCompanyId($companyId)
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * @inheritdoc
     */
    public function getCompanyId()
    {
        return $this->_get(self::COMPANY_ID);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\TransactionParametersExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
