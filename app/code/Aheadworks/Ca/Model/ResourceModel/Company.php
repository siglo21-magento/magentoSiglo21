<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\CompanyInterface;

/**
 * Class CompanyResource
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class Company extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ca_company';

    /**
     * Company payments table name
     */
    const COMPANY_PAYMENTS_TABLE_NAME = 'aw_ca_company_payments';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, CompanyInterface::ID);
    }
}
