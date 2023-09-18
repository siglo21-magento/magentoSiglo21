<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ConfigProvider
 *
 * @package Aheadworks\CreditLimit\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Payment method code
     */
    const METHOD_CODE = 'aw_credit_limit';

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CustomerManagementInterface $customerManagement
     * @param CheckoutSession $session
     * @param PaymentHelper $paymentHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CustomerManagementInterface $customerManagement,
        CheckoutSession $session,
        PaymentHelper $paymentHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->customerManagement = $customerManagement;
        $this->session = $session;
        $this->paymentHelper = $paymentHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function getConfig(): array
    {
        $paymentMethod = $this->paymentHelper->getMethodInstance(self::METHOD_CODE);
        $quote = $this->session->getQuote();
        return $paymentMethod->isAvailable($quote) ? [
            'payment' => [
                self::METHOD_CODE => $this->getPaymentData($quote)
            ]
        ] : [];
    }

    /**
     * Get payment data
     *
     * @param Quote $quote
     * @throws NoSuchEntityException
     * @return array
     */
    private function getPaymentData($quote)
    {
        $store = $this->storeManager->getStore($quote->getStoreId());
        return [
            SummaryInterface::CREDIT_AVAILABLE => $this->customerManagement->getCreditAvailableAmount(
                $quote->getCustomerId(),
                $store->getCurrentCurrency()->getCode()
            )
        ];
    }
}
