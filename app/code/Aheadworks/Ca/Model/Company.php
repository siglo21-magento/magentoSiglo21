<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Company\Validator;
use Aheadworks\Ca\Model\ResourceModel\Company as CompanyResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Company
 * @package Aheadworks\Ca\Model
 */
class Company extends AbstractModel implements CompanyInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Validator $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Validator $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CompanyResourceModel::class);
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
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getRootGroupId()
    {
        return $this->getData(self::ROOT_GROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRootGroupId($id)
    {
        return $this->setData(self::ROOT_GROUP_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getLegalName()
    {
        return $this->getData(self::LEGAL_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setLegalName($name)
    {
        return $this->setData(self::LEGAL_NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getTaxId()
    {
        return $this->getData(self::TAX_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTaxId($taxId)
    {
        return $this->setData(self::TAX_ID, $taxId);
    }

    /**
     * @inheritDoc
     */
    public function getReSellerId()
    {
        return $this->getData(self::RE_SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setReSellerId($reSellerId)
    {
        return $this->setData(self::RE_SELLER_ID, $reSellerId);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * @inheritDoc
     */
    public function getNotes()
    {
        return $this->getData(self::NOTES);
    }

    /**
     * @inheritDoc
     */
    public function setNotes($notes)
    {
        return $this->setData(self::NOTES, $notes);
    }

    /**
     * @inheritDoc
     */
    public function getSalesRepresentativeId()
    {
        return $this->getData(self::SALES_REPRESENTATIVE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSalesRepresentativeId($salesRepresentativeId)
    {
        return $this->setData(self::SALES_REPRESENTATIVE_ID, $salesRepresentativeId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerGroupId()
    {
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * @inheritDoc
     */
    public function getIsAllowedToQuote()
    {
        return $this->getData(self::IS_ALLOWED_TO_QUOTE);
    }

    /**
     * @inheritDoc
     */
    public function setIsAllowedToQuote($isAllowedToQuote)
    {
        return $this->setData(self::IS_ALLOWED_TO_QUOTE, $isAllowedToQuote);
    }

    /**
     * @inheritDoc
     */
    public function getIsApprovedNotificationSent()
    {
        return $this->getData(self::IS_APPROVED_NOTIFICATION_SENT);
    }

    /**
     * @inheritDoc
     */
    public function setIsApprovedNotificationSent($isApprovedNotificationSent)
    {
        return $this->setData(self::IS_APPROVED_NOTIFICATION_SENT, $isApprovedNotificationSent);
    }

    /**
     * @inheritDoc
     */
    public function getIsDeclinedNotificationSent()
    {
        return $this->getData(self::IS_DECLINED_NOTIFICATION_SENT);
    }

    /**
     * @inheritDoc
     */
    public function setIsDeclinedNotificationSent($isDeclinedNotificationSent)
    {
        return $this->setData(self::IS_DECLINED_NOTIFICATION_SENT, $isDeclinedNotificationSent);
    }

    /**
     * @inheritDoc
     */
    public function getAllowedPaymentMethods()
    {
        return $this->getData(self::ALLOWED_PAYMENT_METHODS);
    }

    /**
     * @inheritDoc
     */
    public function setAllowedPaymentMethods($allowedPaymentMethods)
    {
        return $this->setData(self::ALLOWED_PAYMENT_METHODS, $allowedPaymentMethods);
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
        \Aheadworks\Ca\Api\Data\CompanyExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
