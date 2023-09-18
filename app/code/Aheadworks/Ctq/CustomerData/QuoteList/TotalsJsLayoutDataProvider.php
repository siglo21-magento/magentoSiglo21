<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\CustomerData\QuoteList;

use Magento\Tax\CustomerData\CheckoutTotalsJsLayoutDataProvider;

/**
 * Class TotalsJsLayoutDataProvider
 * @package Aheadworks\Ctq\CustomerData\QuoteList
 */
class TotalsJsLayoutDataProvider extends CheckoutTotalsJsLayoutDataProvider
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'components' => [
                'mini_quotelist_content' => [
                    'children' => [
                        'subtotal.container' => [
                            'children' => [
                                'subtotal' => [
                                    'config' => $this->getTotalsConfig()
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
