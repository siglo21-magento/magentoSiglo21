<?php
/**
 * Add file comment to orders
 * Copyright (C) 2018
 *
 * This file is part of Aventi/OrderComment.
 *
 * Aventi/OrderComment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aventi\SAP\Block\Invoice;

use Aventi\SAP\Helper\Data;
use Aventi\SAP\Model\Sync\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;

class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var Customer
     */
    private $customerSync;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CustomerSession $customerSession
     * @param Data $helperData
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param Customer $customerSync
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerSession $customerSession,
        Data $helperData,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Customer $customerSync,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->customerSync = $customerSync;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInvoices(): array
    {
        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
        if ($customer) {
            $cardCode = $customer->getCustomAttribute('sap_customer_id');
            $cardCode = $cardCode ? $cardCode->getValue() : null;
            if ($cardCode) {
                return $this->customerSync->getInvoiceByCustomer($cardCode);
            }
        }
        return [];
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
