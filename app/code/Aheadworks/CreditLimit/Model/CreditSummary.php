<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary as CreditSummaryResource;

/**
 * Class CreditSummary
 *
 * @package Aheadworks\CreditLimit\Model
 */
class CreditSummary extends AbstractModel implements SummaryInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(CreditSummaryResource::class);
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
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
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
    public function setIsCustomCreditLimit($isCustomCreditLimit)
    {
        return $this->setData(self::IS_CUSTOM_CREDIT_LIMIT, $isCustomCreditLimit);
    }

    /**
     * @inheritdoc
     */
    public function getIsCustomCreditLimit()
    {
        return $this->getData(self::IS_CUSTOM_CREDIT_LIMIT);
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
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setLastPaymentDate($lastPaymentDate)
    {
        return $this->setData(self::LAST_PAYMENT_DATE, $lastPaymentDate);
    }

    /**
     * @inheritdoc
     */
    public function getLastPaymentDate()
    {
        return $this->getData(self::LAST_PAYMENT_DATE);
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
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
