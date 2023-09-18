<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;

/**
 * Class Transaction
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
class Transaction extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_cl_transaction';

    /**
     * Transaction entity table
     */
    const TRANSACTION_ENTITY_TABLE = 'aw_cl_transaction_entity';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TransactionInterface::ID);
    }
}
