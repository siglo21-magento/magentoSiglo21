<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface ExternalQuoteInterface
 * @api
 */
interface ExternalQuoteInterface extends QuoteInterface
{
    /**
     * Get cart
     *
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteCartInterface
     */
    public function getCart();

    /**
     * Set cart
     *
     * @param \Aheadworks\Ctq\Api\Data\ExternalQuoteCartInterface $cart
     * @return $this
     */
    public function setCart($cart);
}
