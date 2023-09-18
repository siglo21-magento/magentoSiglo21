<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\QuoteList\Item\Renderer\Actions;

use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;

/**
 * Class Edit
 * @package Aheadworks\Ctq\Block\Cart\Item\Renderer\Actions
 */
class Edit extends Generic
{
    /**
     * Get quote list item configure url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl(
            'checkout/cart/configure',
            [
                'id' => $this->getItem()->getId(),
                'product_id' => $this->getItem()->getProduct()->getId(),
                Checker::AW_CTQ_QUOTE_LIST_FLAG => '1'
            ]
        );
    }
}
