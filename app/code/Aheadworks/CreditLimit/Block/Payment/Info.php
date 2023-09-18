<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Block\Payment;

use Magento\Payment\Block\Info as BaseInfo;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Info
 *
 * @package Aheadworks\CreditLimit\Block\Payment
 */
class Info extends BaseInfo
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_CreditLimit::payment/info.phtml';

    /**
     * Get purchase order number
     *
     * @return string|null
     * @throws LocalizedException
     */
    public function getPoNumber()
    {
        return $this->getInfo()->getPoNumber();
    }
}
