<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\QuoteList\ProductList\Item;

use Magento\Catalog\Block\Product\ProductList\Item\Block;

/**
 * Class AddButton
 * @package Aheadworks\Ctq\Block\QuoteList\ProductList\Item
 */
class AddButton extends Block
{
    /**
     * Retrieve post params
     *
     * @return string
     */
    public function getAddToQuoteListParams()
    {
        $data = ['product' => $this->getProduct()->getId()];
        $url = $this->getUrl('aw_ctq/quoteList/add', ['_secure' => true]);

        return json_encode([
            'action' => $url,
            'data' => $data
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getProduct()
    {
        $product = parent::getProduct();

        return $product ?: $this->_coreRegistry->registry('product');
    }
}
