<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Source\Company;

/**
 * Class PaymentMethodList
 * @package Aheadworks\Ca\Model\Source\Company
 */
class PaymentMethodList extends PaymentMethod
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->thirdPartyModuleManager->isAwPayRestModuleEnabled()) {
            $payments = $this->getPaymentManagement()->getPaymentMethods();
            $options = $this->getPaymentOptions($payments);
        }

        return $options;
    }
}
