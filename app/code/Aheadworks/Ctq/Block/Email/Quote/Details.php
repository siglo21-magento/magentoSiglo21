<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Email\Quote;

use Magento\Framework\View\Element\Template;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\RendererList;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Block\Cart\Totals as CartTotalsBlock;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class Details
 *
 * @method ArgumentInterface|null getViewModel()
 * @method QuoteInterface|null getQuote()
 * @method StoreInterface|null getStore()
 *
 * @package Aheadworks\Ctq\Block\Email\Quote
 */
class Details extends Template
{
    /**
     * Get item row html
     *
     * @param QuoteItem|CartItemInterface $item
     * @param string $itemType
     * @return  string
     */
    public function getItemHtml($item, $itemType)
    {
        $itemHtml = '';
        /** @var RendererList $rendererList */
        $rendererList = $this->getChildBlock('item.list.renderer');
        if (!$rendererList) {
            throw new \RuntimeException(
                'Items list renderer for block "' . $this->getNameInLayout() . '" is not defined'
            );
        }
        $rendererBlock = $rendererList->getRenderer($itemType, 'default');
        if ($rendererBlock) {
            $rendererBlock
                ->setData('item', $item)
                ->setData('is_edit', false)
            ;
            $itemHtml = $rendererBlock->toHtml();
        }
        return $itemHtml;
    }

    /**
     * Retrieve totals html
     *
     * @param CartInterface|Quote $cart
     * @return string
     */
    public function getTotalsHtml($cart)
    {
        /** @var CartTotalsBlock $totalsRenderer */
        $totalsRenderer = $this->getChildBlock('totals.renderer');
        if (!$totalsRenderer) {
            throw new \RuntimeException(
                'Totals renderer for block "' . $this->getNameInLayout() . '" is not defined'
            );
        }
        $totalsRenderer->setData('custom_quote', $cart);
        $totalsHtml =
            $totalsRenderer->renderTotals(null, 3)
            . $totalsRenderer->renderTotals('footer', 3)
        ;
        return $totalsHtml;
    }
}
