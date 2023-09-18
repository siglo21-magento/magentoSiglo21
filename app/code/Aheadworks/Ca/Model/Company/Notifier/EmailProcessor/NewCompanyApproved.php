<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

/**
 * Class NewCompanyApproved
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
class NewCompanyApproved extends AbstractProcessor implements EmailProcessorInterface
{
    /**
     * @inheritdoc
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getNewCompanyApprovedTemplate($storeId);
    }

    /**
     * @inheritdoc
     */
    protected function getRecipientName($company)
    {
        $customer = $this->getRootCustomer($company);
        return $customer->getFirstname() . ' ' .  $customer->getLastname();
    }

    /**
     * @inheritdoc
     */
    protected function getRecipientEmail($company)
    {
        $customer = $this->getRootCustomer($company);
        return $customer->getEmail();
    }
}
