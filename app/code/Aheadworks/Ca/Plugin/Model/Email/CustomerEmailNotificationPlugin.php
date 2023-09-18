<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Model\Email;

use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CustomerEmailNotificationPlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\Email
 */
class CustomerEmailNotificationPlugin
{
    /**
     * Disable native email notification of newly created company user
     *
     * @param EmailNotificationInterface $subject
     * @param callable $proceed
     * @param CustomerInterface $customer
     * @param string $type
     * @param string $backUrl
     * @param string|int $storeId
     * @param string|null $sendEmailStoreId
     * @return mixed
     */
    public function aroundNewAccount(
        EmailNotificationInterface $subject,
        callable $proceed,
        CustomerInterface $customer,
        $type = EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $sendEmailStoreId = null
    ) {
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            return null;
        }

        return $proceed($customer, $type, $backUrl, $storeId, $sendEmailStoreId);
    }
}
