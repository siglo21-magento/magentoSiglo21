<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Sales\Api\Data\CreditmemoInterface as SalesCreditmemoInterface;

/**
 * Interface CreditmemoInterface
 * @api
 */
interface CreditmemoInterface extends SalesCreditmemoInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_CTQ_AMOUNT = 'aw_ctq_amount';
    const BASE_AW_CTQ_AMOUNT = 'base_aw_ctq_amount';
    /**#@-*/
}
