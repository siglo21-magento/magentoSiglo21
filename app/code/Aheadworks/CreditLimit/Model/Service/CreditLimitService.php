<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Model\Transaction\TransactionParametersFactory;
use Aheadworks\CreditLimit\Api\TransactionManagementInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CreditLimitService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CreditLimitService implements CreditLimitManagementInterface
{
    /**
     * @var TransactionManagementInterface
     */
    private $transactionService;

    /**
     * @var TransactionParametersFactory
     */
    private $transactionParametersFactory;

    /**
     * @param TransactionManagementInterface $transactionService
     * @param TransactionParametersFactory $transactionParametersFactory
     */
    public function __construct(
        TransactionManagementInterface $transactionService,
        TransactionParametersFactory $transactionParametersFactory
    ) {
        $this->transactionService = $transactionService;
        $this->transactionParametersFactory = $transactionParametersFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function updateCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    ) {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_LIMIT_CHANGED,
                TransactionParametersInterface::CREDIT_LIMIT => $creditLimit,
                TransactionParametersInterface::IS_CUSTOM_CREDIT_LIMIT => true,
                TransactionParametersInterface::COMMENT_TO_ADMIN => $commentToAdmin,
                TransactionParametersInterface::COMMENT_TO_CUSTOMER => $commentToCustomer
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function updateDefaultCreditLimit(
        $customerId,
        $creditLimit,
        $commentToAdmin = '',
        $commentToCustomer = ''
    ) {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_LIMIT_CHANGED,
                TransactionParametersInterface::CREDIT_LIMIT => $creditLimit,
                TransactionParametersInterface::IS_CUSTOM_CREDIT_LIMIT => false,
                TransactionParametersInterface::COMMENT_TO_ADMIN => $commentToAdmin,
                TransactionParametersInterface::COMMENT_TO_CUSTOMER => $commentToCustomer
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function updateCreditBalance(
        $customerId,
        $amount,
        $currency = null,
        $commentToAdmin = '',
        $commentToCustomer = '',
        $poNumber = ''
    ) {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_BALANCE_UPDATED,
                TransactionParametersInterface::AMOUNT => $amount,
                TransactionParametersInterface::AMOUNT_CURRENCY => $currency,
                TransactionParametersInterface::USED_CURRENCY => $currency,
                TransactionParametersInterface::COMMENT_TO_ADMIN => $commentToAdmin,
                TransactionParametersInterface::COMMENT_TO_CUSTOMER => $commentToCustomer,
                TransactionParametersInterface::PO_NUMBER => $poNumber,
                TransactionParametersInterface::IS_ALLOWED_TO_EXCEED_LIMIT => true
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function spendCreditBalanceOnOrder($customerId, $order)
    {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::ORDER_PURCHASED,
                TransactionParametersInterface::AMOUNT => -$order->getBaseGrandTotal(),
                TransactionParametersInterface::AMOUNT_CURRENCY => $order->getBaseCurrencyCode(),
                TransactionParametersInterface::USED_CURRENCY => $order->getOrderCurrencyCode(),
                TransactionParametersInterface::PO_NUMBER => $order->getPayment()->getPoNumber(),
                TransactionParametersInterface::ORDER_ENTITY => $order
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function reimburseCreditBalanceOnCanceledOrder($customerId, $order)
    {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::ORDER_CANCELED,
                TransactionParametersInterface::AMOUNT => $order->getBaseGrandTotal(),
                TransactionParametersInterface::AMOUNT_CURRENCY => $order->getBaseCurrencyCode(),
                TransactionParametersInterface::USED_CURRENCY => $order->getOrderCurrencyCode(),
                TransactionParametersInterface::ORDER_ENTITY => $order
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function refundCreditBalanceOnCreditmemo($customerId, $order, $creditmemo)
    {
        $transactionParams = $this->transactionParametersFactory->create(
            [
                TransactionParametersInterface::CUSTOMER_ID => $customerId,
                TransactionParametersInterface::ACTION => Action::CREDIT_MEMO_REFUNDED,
                TransactionParametersInterface::AMOUNT => $creditmemo->getBaseGrandTotal(),
                TransactionParametersInterface::AMOUNT_CURRENCY => $creditmemo->getBaseCurrencyCode(),
                TransactionParametersInterface::USED_CURRENCY => $creditmemo->getOrderCurrencyCode(),
                TransactionParametersInterface::ORDER_ENTITY => $order,
                TransactionParametersInterface::CREDITMEMO_ENTITY => $creditmemo
            ]
        );
        $this->transactionService->createTransaction($transactionParams);

        return true;
    }
}
