<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

/**
 * Class CompanyStatusChanged
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
class CompanyStatusChanged extends NewCompanyApproved implements EmailProcessorInterface
{
    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getCompanyStatusChangedTemplate($storeId);
    }
}
