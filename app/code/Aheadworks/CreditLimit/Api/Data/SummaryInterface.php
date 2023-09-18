<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SummaryInterface
 * @api
 */
interface SummaryInterface extends ExtensibleDataInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const SUMMARY_ID = 'summary_id';
    const CUSTOMER_ID = 'customer_id';
    const WEBSITE_ID = 'website_id';
    const CREDIT_LIMIT = 'credit_limit';
    const IS_CUSTOM_CREDIT_LIMIT = 'is_custom_credit_limit';
    const CREDIT_BALANCE = 'credit_balance';
    const CREDIT_AVAILABLE = 'credit_available';
    const CURRENCY = 'currency';
    const LAST_PAYMENT_DATE = 'last_payment_date';
    const COMPANY_ID = 'company_id';
    /**#@-*/

    /**
     * Set summary ID
     *
     * @param int $summaryId
     * @return SummaryInterface
     */
    public function setSummaryId($summaryId);

    /**
     * Get summary ID
     *
     * @return int
     */
    public function getSummaryId();

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return SummaryInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set website ID
     *
     * @param int $websiteId
     * @return SummaryInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * Get website ID
     *
     * @return int
     */
    public function getWebsiteId();

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
     * @return float|null
     */
    public function getCreditLimit();

    /**
     * Set is custom credit limit
     *
     * @param bool $isCustomCreditLimit
     * @return SummaryInterface
     */
    public function setIsCustomCreditLimit($isCustomCreditLimit);

    /**
     * Get is custom credit limit
     *
     * @return bool
     */
    public function getIsCustomCreditLimit();

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
     * @return float
     */
    public function setCreditAvailable($creditAvailable);

    /**
     * Get credit available
     *
     * @return float
     */
    public function getCreditAvailable();

    /**
     * Set credit limit currency
     *
     * @param string $currency
     * @return SummaryInterface
     */
    public function setCurrency($currency);

    /**
     * Get credit limit currency
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set last payment date
     *
     * @param string $lastPaymentDate
     * @return SummaryInterface
     */
    public function setLastPaymentDate($lastPaymentDate);

    /**
     * Get last payment date
     *
     * @return string
     */
    public function getLastPaymentDate();

    /**
     * Set company ID
     *
     * @param int $companyId
     * @return SummaryInterface
     */
    public function setCompanyId($companyId);

    /**
     * Get company ID
     *
     * @return string
     */
    public function getCompanyId();

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\SummaryExtensionInterface $extensionAttributes
    );
}
