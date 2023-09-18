<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class Payment
 *
 * @package Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider
 */
class Payment implements ConfigProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $output['payment'] = [
            'klarna_kp' => [],
            'customerBalance' => [],
            'awStoreCredit' => []
        ];
        $output['awGiftcard'] = [];

        return $output;
    }
}
