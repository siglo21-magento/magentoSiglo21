<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Sales\Api\Data\OrderItemInterface as SalesOrderItemInterface;

/**
 * Interface OrderItemInterface
 * @api
 */
interface OrderItemInterface extends SalesOrderItemInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_CTQ_AMOUNT = 'aw_ctq_amount';
    const BASE_AW_CTQ_AMOUNT = 'base_aw_ctq_amount';
    const AW_CTQ_INVOICED = 'aw_ctq_invoiced';
    const BASE_AW_CTQ_INVOICED = 'base_aw_ctq_invoiced';
    const AW_CTQ_REFUNDED = 'aw_ctq_refunded';
    const BASE_AW_CTQ_REFUNDED = 'base_aw_ctq_refunded';
    const AW_CTQ_PERCENT = 'aw_ctq_percent';
    /**#@-*/
}
