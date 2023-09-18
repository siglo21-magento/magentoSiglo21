<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResourceModel;

/**
 * Class Transaction
 *
 * @package Aheadworks\CreditLimit\Model
 */
class Transaction extends AbstractModel implements TransactionInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(TransactionResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setSummaryId($summaryId)
    {
        return $this->setData(self::SUMMARY_ID, $summaryId);
    }

    /**
     * @inheritdoc
     */
    public function getSummaryId()
    {
        return $this->getData(self::SUMMARY_ID);
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
        return $this->getData(self::COMPANY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
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
        return $this->getData(self::ACTION);
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
        return $this->getData(self::AMOUNT);
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
        return $this->getData(self::CREDIT_LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setCreditBalance($creditBalance)
    {
        return $this->setData(self::CREDIT_BALANCE, $creditBalance);
    }

    /**
     * @inheritdoc
     */
    public function getCreditBalance()
    {
        return $this->getData(self::CREDIT_BALANCE);
    }

    /**
     * @inheritdoc
     */
    public function setCreditAvailable($creditAvailable)
    {
        return $this->setData(self::CREDIT_AVAILABLE, $creditAvailable);
    }

    /**
     * @inheritdoc
     */
    public function getCreditAvailable()
    {
        return $this->getData(self::CREDIT_AVAILABLE);
    }

    /**
     * @inheritdoc
     */
    public function setCreditCurrency($creditCurrency)
    {
        return $this->setData(self::CREDIT_CURRENCY, $creditCurrency);
    }

    /**
     * @inheritdoc
     */
    public function getCreditCurrency()
    {
        return $this->getData(self::CREDIT_CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setActionCurrency($actionCurrency)
    {
        return $this->setData(self::ACTION_CURRENCY, $actionCurrency);
    }

    /**
     * @inheritdoc
     */
    public function getActionCurrency()
    {
        return $this->getData(self::ACTION_CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setRateToCreditCurrency($rateToCreditCurrency)
    {
        return $this->setData(self::RATE_TO_CREDIT_CURRENCY, $rateToCreditCurrency);
    }

    /**
     * @inheritdoc
     */
    public function getRateToCreditCurrency()
    {
        return $this->getData(self::RATE_TO_CREDIT_CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setRateToActionCurrency($rateToActionCurrency)
    {
        return $this->setData(self::RATE_TO_ACTION_CURRENCY, $rateToActionCurrency);
    }

    /**
     * @inheritdoc
     */
    public function getRateToActionCurrency()
    {
        return $this->getData(self::RATE_TO_ACTION_CURRENCY);
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
        return $this->getData(self::PO_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedBy($updatedBy)
    {
        return $this->setData(self::UPDATED_BY, $updatedBy);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedBy()
    {
        return $this->getData(self::UPDATED_BY);
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
        return $this->getData(self::COMMENT_TO_CUSTOMER);
    }

    /**
     * @inheritdoc
     */
    public function setCommentToCustomerPlaceholder($commentToCustomerPlaceholder)
    {
        return $this->setData(self::COMMENT_TO_CUSTOMER_PLACEHOLDER, $commentToCustomerPlaceholder);
    }

    /**
     * @inheritdoc
     */
    public function getCommentToCustomerPlaceholder()
    {
        return $this->getData(self::COMMENT_TO_CUSTOMER_PLACEHOLDER);
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
        return $this->getData(self::COMMENT_TO_ADMIN);
    }

    /**
     * @inheritdoc
     */
    public function setEntities($entities)
    {
        return $this->setData(self::ENTITIES, $entities);
    }

    /**
     * @inheritdoc
     */
    public function getEntities()
    {
        return $this->getData(self::ENTITIES);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\TransactionExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
