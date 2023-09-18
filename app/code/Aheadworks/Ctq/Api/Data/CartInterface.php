<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Quote\Api\Data\CartInterface as QuoteCartInterfaceInterface;

/**
 * Interface CartInterface
 * @api
 */
interface CartInterface extends QuoteCartInterfaceInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_CTQ_AMOUNT = 'aw_ctq_amount';
    const BASE_AW_CTQ_AMOUNT = 'base_aw_ctq_amount';
    const AW_CTQ_SELLER_ID = 'aw_ctq_seller_id';
    const AW_CTQ_QUOTE_LIST_CUSTOMER_ID = 'aw_ctq_quote_list_customer_id';
    /**#@-*/
}
