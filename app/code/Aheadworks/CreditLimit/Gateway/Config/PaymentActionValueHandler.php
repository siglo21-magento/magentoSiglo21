<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Gateway\Config;

use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class PaymentActionValueHandler
 *
 * @package Aheadworks\CreditLimit\Gateway\Config
 */
class PaymentActionValueHandler implements ValueHandlerInterface
{
    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param ConfigInterface $configInterface
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ConfigInterface $configInterface,
        SubjectReader $subjectReader
    ) {
        $this->configInterface = $configInterface;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $subject, $storeId = null)
    {
        $orderStatus = $this->configInterface->getValue('order_status', $storeId);
        if ($orderStatus != SalesOrder::STATE_PROCESSING) {
            $result = AbstractMethod::ACTION_ORDER;
        } else {
            $result = $this->configInterface->getValue($this->subjectReader->readField($subject), $storeId);
        }

        return $result;
    }
}
