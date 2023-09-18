<?php

namespace Aventi\SAP\Api\Data;

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
    const LINE_SAP = 'line_sap';
    const BASE_ENTRY_SAP = 'base_entry';
    const BASE_TYPE_SAP = 'base_type';
}
