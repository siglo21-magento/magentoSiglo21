<?php

namespace Aventi\SAP\Api\Data;

/**
 * Interface CartItemInterface
 * @api
 */
interface QuoteStatusInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const QUOTE_CREATED = 'created';
    const QUOTE_UPDATED = 'updated';
    const QUOTE_SYNC = 'sync';
    const QUOTE_PROCESSING = 'processing';
}
