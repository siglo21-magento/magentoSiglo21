<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\QuotedItems\Price;

use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\AbstractEdit;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\Product\Type as ProductType;

/**
 * Class Tier
 *
 * @method TierInfo setItem(Item $item)
 * @method Item getItem()
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\QuotedItems\Price
 */
class TierInfo extends AbstractEdit
{
    /**
     * Get tier price html
     *
     * @param Item $item
     * @return string
     */
    public function getTierHtml(Item $item)
    {
        $html = '';
        $prices = $item->getProduct()->getTierPrice();
        if ($prices) {
            if ($item->getProductType() == ProductType::TYPE_BUNDLE) {
                $info = $this->getBundleTierPriceInfo($prices);
            } else {
                $info = $this->getTierPriceInfo($prices);
            }

            $html = implode('<br />', $info);
        }
        return $html;
    }

    /**
     * Get tier price info to display in grid for Bundle product
     *
     * @param array $prices
     * @return string[]
     */
    private function getBundleTierPriceInfo($prices)
    {
        $info = [];
        foreach ($prices as $data) {
            $qty = $data['price_qty'] * 1;
            $info[] = __('%1 with %2 discount each', $qty, $data['price'] * 1 . '%');
        }
        return $info;
    }

    /**
     * Get tier price info to display in grid
     *
     * @param array $prices
     * @return string[]
     */
    private function getTierPriceInfo($prices)
    {
        $info = [];
        foreach ($prices as $data) {
            $qty = $data['price_qty'] * 1;
            $price = $this->convertPrice($data['price']);
            $info[] = __('%1 for %2', $qty, $price);
        }
        return $info;
    }
}
