<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Quote\Api\Data\CartItemInterface as QuoteCartItemInterfaceInterface;

/**
 * Interface CartItemInterface
 * @api
 */
interface CartItemInterface extends QuoteCartItemInterfaceInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_CTQ_AMOUNT = 'aw_ctq_amount';
    const BASE_AW_CTQ_AMOUNT = 'base_aw_ctq_amount';
    const AW_CTQ_PERCENT = 'aw_ctq_percent';
    const AW_CTQ_CALCULATE_TYPE = 'aw_ctq_calculate_type';
    const AW_CTQ_SORT_ORDER = 'aw_ctq_sort_order';
    /**#@-*/
}
