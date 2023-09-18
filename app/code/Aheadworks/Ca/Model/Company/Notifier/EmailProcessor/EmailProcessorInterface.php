<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

use Aheadworks\Ca\Api\Data\CompanyInterface;
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
     * @param CompanyInterface $company
     * @return EmailMetadataInterface[]
     */
    public function process($company);
}
