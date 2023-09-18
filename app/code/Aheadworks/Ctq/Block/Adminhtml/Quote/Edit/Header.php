<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Header
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class Header extends AbstractEdit
{
    /**
     * Customer repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Customer view helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote $sessionQuote
     * @param \Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote $sessionQuote,
        \Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->getSession()->getOrder()->getId()) {
            return __('Edit Quote #%1', $this->getSession()->getOrder()->getIncrementId());
        }
        $out = $this->_getCreateOrderTitle();
        return $this->escapeHtml($out);
    }

    /**
     * Generate title for new order creation page.
     *
     * @return string
     */
    protected function _getCreateOrderTitle()
    {
        $customerId = $this->getCustomerId();
        $storeId = $this->getStoreId();
        $awQuote = $this->getQuote()->getExtensionAttributes()
            ? $this->getQuote()->getExtensionAttributes()->getAwCtqQuote()
            : null;
        $out = '';

        if ($awQuote) {
            $out = __('Edit "%1"', $awQuote->getName());
            return $out;
        } elseif ($customerId && $storeId) {
            $out .= __(
                'Create New Quote for %1 in %2',
                $this->_getCustomerName($customerId),
                $this->getStore()->getName()
            );
            return $out;
        } elseif (!$customerId && $storeId) {
            $out .= __('Create New Quote in %1', $this->getStore()->getName());
            return $out;
        } elseif ($customerId && !$storeId) {
            $out .= __('Create New Quote for %1', $this->_getCustomerName($customerId));
            return $out;
        } elseif (!$customerId && !$storeId) {
            $out .= __('Create New Quote for New Customer');
            return $out;
        }

        return $out;
    }

    /**
     * Get customer name by his ID
     *
     * @param int $customerId
     * @return string
     */
    protected function _getCustomerName($customerId)
    {
        try {
            $customerData = $this->customerRepository->getById($customerId);
            $name = $this->_customerViewHelper->getCustomerName($customerData);
        } catch (NoSuchEntityException $e) {
            $name = '';
        }
        return $name;
    }
}
