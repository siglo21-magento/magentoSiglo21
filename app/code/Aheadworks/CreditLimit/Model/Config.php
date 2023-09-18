<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\CreditLimit\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_EMAIL_CAN_SEND_EMAIL_ON_BALANCE_UPDATE
        = 'aw_credit_limit/email_settings/can_send_email_on_balance_update';
    const XML_PATH_EMAIL_SENDER = 'aw_credit_limit/email_settings/sender';
    const XML_PATH_EMAIL_CREDIT_BALANCE_UPDATED_TEMPLATE
        = 'aw_credit_limit/email_settings/credit_balance_updated_template';
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
     * Check if allowed to send email on balance update
     *
     * @param int|null $storeId
     * @return string
     */
    public function isAllowedToSendEmailOnBalanceUpdate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_CAN_SEND_EMAIL_ON_BALANCE_UPDATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve credit balance updated email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCreditBalanceUpdatedTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_CREDIT_BALANCE_UPDATED_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
