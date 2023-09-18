<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CompanyInterface
 * @api
 */
interface CompanyInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const ROOT_GROUP_ID = 'root_group_id';
    const STATUS = 'status';
    const NAME = 'name';
    const LEGAL_NAME = 'legal_name';
    const EMAIL = 'email';
    const TAX_ID = 'tax_id';
    const RE_SELLER_ID = 're_seller_id';
    const STREET = 'street';
    const CITY = 'city';
    const COUNTRY_ID = 'country_id';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const POSTCODE = 'postcode';
    const TELEPHONE = 'telephone';
    const SALES_REPRESENTATIVE_ID = 'sales_representative_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const IS_ALLOWED_TO_QUOTE = 'is_allowed_to_quote';
    const IS_APPROVED_NOTIFICATION_SENT = 'is_approved_notification_sent';
    const IS_DECLINED_NOTIFICATION_SENT = 'is_declined_notification_sent';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const NOTES = 'notes';
    const ALLOWED_PAYMENT_METHODS = 'allowed_payment_methods';
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
     * @param int|null $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get root group id
     *
     * @return int
     */
    public function getRootGroupId();

    /**
     * Set root group id
     *
     * @param int $id
     * @return $this
     */
    public function setRootGroupId($id);

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
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get Legal Name
     *
     * @return string
     */
    public function getLegalName();

    /**
     * Set Legal Name
     *
     * @param string $name
     * @return $this
     */
    public function setLegalName($name);

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set Email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get tax id
     *
     * @return string
     */
    public function getTaxId();

    /**
     * Set tax id
     *
     * @param string $taxId
     * @return $this
     */
    public function setTaxId($taxId);

    /**
     * Get re-seller id
     *
     * @return string
     */
    public function getReSellerId();

    /**
     * Set re-seller id
     *
     * @param string $reSellerId
     * @return $this
     */
    public function setReSellerId($reSellerId);

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet();

    /**
     * Set street
     *
     * @param string $street
     * @return $this
     */
    public function setStreet($street);

    /**
     * Get city
     *
     * @return string
     */
    public function getCity();

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * Get country id
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Set country id
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * Get state
     *
     * @return string
     */
    public function getRegion();

    /**
     * Set state
     *
     * @param string $region
     * @return $this
     */
    public function setRegion($region);

    /**
     * Get state id
     *
     * @return string
     */
    public function getRegionId();

    /**
     * Set state id
     *
     * @param string $regionId
     * @return $this
     */
    public function setRegionId($regionId);

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode();

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes();

    /**
     * Set notes
     *
     * @param string $notes
     * @return $this
     */
    public function setNotes($notes);

    /**
     * Set sales representative id
     *
     * @param int $salesRepresentativeId
     * @return $this
     */
    public function setSalesRepresentativeId($salesRepresentativeId);

    /**
     * Get sales representative id
     *
     * @return int|null
     */
    public function getSalesRepresentativeId();

    /**
     * Set customer group id
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getCustomerGroupId();

    /**
     * Set is allowed to quote
     *
     * @param bool $isAllowedToQuote
     * @return $this
     */
    public function setIsAllowedToQuote($isAllowedToQuote);

    /**
     * Get is allowed to quote
     *
     * @return bool
     */
    public function getIsAllowedToQuote();

    /**
     * Set is approved notification sent
     *
     * @param bool $isApprovedNotificationSent
     * @return $this
     */
    public function setIsApprovedNotificationSent($isApprovedNotificationSent);

    /**
     * Get is approved notification sent
     *
     * @return bool
     */
    public function getIsApprovedNotificationSent();

    /**
     * Set is declined notification sent
     *
     * @param bool $isDeclinedNotificationSent
     * @return $this
     */
    public function setIsDeclinedNotificationSent($isDeclinedNotificationSent);

    /**
     * Get is declined notification sent
     *
     * @return bool
     */
    public function getIsDeclinedNotificationSent();

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

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
    public function getUpdatedAt();

    /**
     * Get allowed payment methods
     *
     * @return string[]
     */
    public function getAllowedPaymentMethods();

    /**
     * Set allowed payment methods
     *
     * @param string[] $allowedPaymentMethods
     * @return $this
     */
    public function setAllowedPaymentMethods($allowedPaymentMethods);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\CompanyExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\CompanyExtensionInterface $extensionAttributes
    );
}
