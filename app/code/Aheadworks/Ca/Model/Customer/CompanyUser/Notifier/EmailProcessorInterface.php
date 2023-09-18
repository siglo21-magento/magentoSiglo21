<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser\Notifier;

use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;

/**
 * Interface EmailProcessorInterface
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
interface EmailProcessorInterface
{
    /**
     * Process email
     *
     * @param CustomerInterface $customer
     * @return EmailMetadataInterface[]
     */
    public function process($customer);
}
