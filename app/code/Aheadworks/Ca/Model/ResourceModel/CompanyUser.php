<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CompanyUser
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class CompanyUser extends AbstractDb
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ca_company_user';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_isPkAutoIncrement = false;
        $this->_init(self::MAIN_TABLE_NAME, CompanyUserInterface::CUSTOMER_ID);
    }
}
