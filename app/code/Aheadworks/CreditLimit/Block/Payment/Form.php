<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Block\Payment;

use Magento\Payment\Block\Form as BaseForm;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Form
 *
 * @package Aheadworks\CreditLimit\Block\Payment
 */
class Form extends BaseForm
{
    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var string
     */
    protected $_template = 'Aheadworks_CreditLimit::payment/form.phtml';

    /**
     * @param Context $context
     * @param SessionManagerInterface $session
     * @param CustomerManagementInterface $customerManagement
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        CustomerManagementInterface $customerManagement,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerManagement = $customerManagement;
        $this->priceCurrency = $priceCurrency;
        $this->session = $session;
    }

    /**
     * Get available balance for customer
     *
     * @return float
     */
    public function getCustomerAvailableBalance()
    {
        $amount = $this->getAvailableAmount();
        $result = 0;
        if ($amount) {
            /** @var Quote $quote */
            $quote = $this->session->getQuote();
            $result = $this->priceCurrency->format(
                $amount,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                null,
                $quote->getQuoteCurrencyCode()
            );
        }

        return $result;
    }

    /**
     * Is balance is enough to pay
     *
     * @return bool
     */
    public function isBalanceEnoughToPay()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        $amount = $this->getAvailableAmount();
        return $amount ? $amount >= $quote->getGrandTotal() : false;
    }

    /**
     * Get multishipping grand total
     *
     * @return float
     */
    public function getMultishippingTotal()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        return $quote->getGrandTotal();
    }

    /**
     * Get available amount
     *
     * @return float|null
     */
    private function getAvailableAmount()
    {
        /** @var Quote $quote */
        $quote = $this->session->getQuote();
        $amount = null;
        if ($quote && $quote->getCustomerId()) {
            $amount = $this->customerManagement->getCreditAvailableAmount(
                $quote->getCustomerId(),
                $quote->getQuoteCurrencyCode()
            );
        }

        return $amount;
    }
}
