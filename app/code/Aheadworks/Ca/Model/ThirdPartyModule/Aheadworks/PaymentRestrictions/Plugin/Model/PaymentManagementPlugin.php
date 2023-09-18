<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Plugin\Model;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Model\PaymentManagement;
use Aheadworks\PaymentRestrictions\Model\PaymentManagement as PayRestPaymentManagement;

/**
 * Class PaymentManagementPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Plugin\Model
 */
class PaymentManagementPlugin
{
    /**
     * @var PaymentManagement
     */
    private $paymentManagement;

    /**
     * @param PaymentManagement $paymentManagement
     */
    public function __construct(PaymentManagement $paymentManagement)
    {
        $this->paymentManagement = $paymentManagement;
    }

    /**
     * Is available by method code
     *
     * @param PayRestPaymentManagement $subject
     * @param \Closure $proceed
     * @param $paymentCode
     * @param int|null $group
     * @param int|null $websiteId
     * @return bool
     */
    public function aroundIsAvailable(
        PayRestPaymentManagement $subject,
        \Closure $proceed,
        $paymentCode,
        $group = null,
        $websiteId = null
    ) {
        $allowedPaymentMethods = $this->paymentManagement->getAllowedCompanyPaymentMethods();
        if (!empty($allowedPaymentMethods)) {
            $result = in_array($paymentCode, $allowedPaymentMethods);
        } else {
            $result = $proceed($paymentCode, $group, $websiteId);
        }

        return $result;
    }
}
