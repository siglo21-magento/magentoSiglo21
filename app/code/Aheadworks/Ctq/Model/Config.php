<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\Ctq\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_IS_QUOTE_LIST_ENABLED = 'aw_ctq/general/is_quote_list_enabled';
    const XML_PATH_GENERAL_IS_AUTO_ACCEPT_ENABLED = 'aw_ctq/general/is_auto_accept_enabled';
    const XML_PATH_GENERAL_AUTO_ACCEPT_COMMENT = 'aw_ctq/general/auto_accept_comment';
    const XML_PATH_GENERAL_EXPORT_EXTRA_BLOCK = 'aw_ctq/general/export_extra_block';
    const XML_PATH_GENERAL_CUSTOMER_GROUPS_TO_REQUEST_A_QUOTE = 'aw_ctq/general/customer_groups_to_request_a_quote';
    const XML_PATH_GENERAL_QUOTE_EXPIRATION_PERIOD_IN_DAYS = 'aw_ctq/general/quote_expiration_period_in_days';
    const XML_PATH_GENERAL_QUOTE_ASSIGNED_ADMIN_USER = 'aw_ctq/general/quote_assigned_admin_user';
    const XML_PATH_FILE_ATTACHMENTS_MAX_UPLOAD_FILE_SIZE = 'aw_ctq/file_attachments/max_upload_file_size';
    const XML_PATH_FILE_ATTACHMENTS_ALLOW_FILE_EXTENSIONS = 'aw_ctq/file_attachments/allow_file_extensions';
    const XML_PATH_EMAIL_SENDER = 'aw_ctq/email/sender';
    const XML_PATH_EMAIL_RECIPIENTS = 'aw_ctq/email/recipients';
    const XML_PATH_EMAIL_SELLER_TEMPLATE_QUOTE_CHANGES = 'aw_ctq/email/seller_template_quote_changes';
    const XML_PATH_EMAIL_BUYER_TEMPLATE_QUOTE_CHANGES = 'aw_ctq/email/buyer_template_quote_changes';
    const XML_PATH_EMAIL_SELLER_TEMPLATE_NEW_QUOTE = 'aw_ctq/email/seller_template_new_quote';
    const XML_PATH_EMAIL_BUYER_TEMPLATE_NEW_QUOTE = 'aw_ctq/email/buyer_template_new_quote';
    const XML_PATH_EMAIL_SEND_EMAIL_REMINDER_IN_DAYS = 'aw_ctq/email/send_email_reminder_in_days';
    const XML_PATH_EMAIL_EXPIRATION_REMINDER_TEMPLATE = 'aw_ctq/email/expiration_reminder_template';
    const XML_PATH_EMAIL_QUOTE_ADMIN_CHANGE_TEMPLATE = 'aw_ctq/email/quote_admin_change_template';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is quote list enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isQuoteListEnabled($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_QUOTE_LIST_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Is quote auto accept enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isAutoAcceptEnabled($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_AUTO_ACCEPT_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get quote auto accept comment
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAutoAcceptComment($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_AUTO_ACCEPT_COMMENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get export extra block
     *
     * @param int|null $storeId
     * @return string
     */
    public function getExportExtraBlock($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_EXPORT_EXTRA_BLOCK,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve customer groups that are allowed to request a quote
     *
     * @param int|null $websiteId
     * @return array
     */
    public function getCustomerGroupsToRequestQuote($websiteId = null)
    {
        $groups = $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_CUSTOMER_GROUPS_TO_REQUEST_A_QUOTE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return empty($groups) ? [] : explode(',', $groups);
    }

    /**
     * Retrieve number of days, quote will expire
     *
     * @param int|null $storeId
     * @return int
     */
    public function getQuoteExpirationPeriodInDays($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_QUOTE_EXPIRATION_PERIOD_IN_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve backend user ID assigned to quote
     *
     * @return int|null
     */
    public function getQuoteAssignedAdminUser()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_QUOTE_ASSIGNED_ADMIN_USER);
    }

    /**
     * Retrieve max upload file size
     *
     * @param int|null $storeId
     * @return int
     */
    public function getMaxUploadFileSize($storeId = null)
    {
        $fileSizeMb = (int)$this->scopeConfig->getValue(
            self::XML_PATH_FILE_ATTACHMENTS_MAX_UPLOAD_FILE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $fileSizeMb * 1024 * 1024;
    }

    /**
     * Retrieve allow file extensions
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAllowFileExtensions($storeId = null)
    {
        $extensions = $this->scopeConfig->getValue(
            self::XML_PATH_FILE_ATTACHMENTS_ALLOW_FILE_EXTENSIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return empty($extensions) ? [] : explode(',', $extensions);
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
     * Retrieve recipients email
     *
     * @param int|null $storeId
     * @return array
     */
    public function getRecipientsEmail($storeId = null)
    {
        $recipients = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_RECIPIENTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return array_map('trim', array_filter(explode(',', $recipients)));
    }

    /**
     * Retrieve seller quote changes email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSellerQuoteChangesTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SELLER_TEMPLATE_QUOTE_CHANGES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve buyer quote changes email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getBuyerQuoteChangesTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_BUYER_TEMPLATE_QUOTE_CHANGES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve number of days, reminder emails should be sent
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSendEmailReminderInDays($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SEND_EMAIL_REMINDER_IN_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve buyer new quote email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getBuyerNewQuoteTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_BUYER_TEMPLATE_NEW_QUOTE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve seller new quote email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSellerNewQuoteTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SELLER_TEMPLATE_NEW_QUOTE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve expiration reminder template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getExpirationReminderTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_EXPIRATION_REMINDER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve expiration reminder template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getQuoteAdminChangeTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_QUOTE_ADMIN_CHANGE_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
