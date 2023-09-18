<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class RefundCommand
 *
 * @package Aheadworks\CreditLimit\Gateway\Command
 */
class RefundCommand implements CommandInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var CreditLimitManagementInterface
     */
    private $creditLimitManagement;

    /**
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        CreditLimitManagementInterface $creditLimitManagement,
        SubjectReader $subjectReader
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $paymentDataObject = $this->subjectReader->readPayment($commandSubject);
        $payment = $paymentDataObject->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException(__('Order payment is not provided.'));
        }

        /** @var OrderInterface $order */
        $order = $payment->getOrder();
        if ($order->getCustomerId()) {
            $this->creditLimitManagement->refundCreditBalanceOnCreditmemo(
                $order->getCustomerId(),
                $order,
                $payment->getCreditmemo()
            );
        }
    }
}
