<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Aheadworks\Ca\Model\Source\Admin\User as AdminUserSource;

/**
 * Class Config
 *
 * @package Aheadworks\Ca\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_DEFAULT_SALES_REPRESENTATIVE = 'aw_ca/general/default_sales_representative';
    const XML_PATH_EMAIL_SENDER = 'aw_ca/email/sender';
    const XML_PATH_EMAIL_NEW_COMPANY_APPROVED_TEMPLATE = 'aw_ca/email/new_company_approved_template';
    const XML_PATH_EMAIL_NEW_COMPANY_SUBMITTED_TEMPLATE = 'aw_ca/email/new_company_submitted_template';
    const XML_PATH_EMAIL_NEW_COMPANY_DECLINED_TEMPLATE = 'aw_ca/email/new_company_declined_template';
    const XML_PATH_EMAIL_COMPANY_STATUS_CHANGED_TEMPLATE = 'aw_ca/email/company_status_changed_template';
    const XML_PATH_EMAIL_NEW_COMPANY_USER_CREATED_TEMPLATE = 'aw_ca/email/new_company_user_created_template';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var AdminUserSource
     */
    private $adminSource;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param AdminUserSource $adminSource
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AdminUserSource $adminSource
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->adminSource = $adminSource;
    }

    /**
     * Retrieve backend user ID as sales representative
     *
     * @param int|null $websiteId
     * @return int|null
     */
    public function getDefaultSalesRepresentative($websiteId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_DEFAULT_SALES_REPRESENTATIVE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        $adminUsers = $this->adminSource->toOptionArray();
        if (!$value && !empty($adminUsers)) {
            $adminUser = reset($adminUsers);
            $value = $adminUser['value'];
        }

        return (int)$value;
    }

    /**
     * Retrieve email sender
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sender name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderName($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sender email
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderEmail($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company approved email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyApprovedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_APPROVED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company submitted email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanySubmittedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_SUBMITTED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company declined email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyDeclinedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_DECLINED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve company status changed email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCompanyStatusChangedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_COMPANY_STATUS_CHANGED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new company user created email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewCompanyUserCreatedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_COMPANY_USER_CREATED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
