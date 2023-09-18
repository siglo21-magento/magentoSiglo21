<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

/**
 * Class NewCompanyDeclined
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
class NewCompanyDeclined extends NewCompanyApproved
{
    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getNewCompanyDeclinedTemplate($storeId);
    }
}
