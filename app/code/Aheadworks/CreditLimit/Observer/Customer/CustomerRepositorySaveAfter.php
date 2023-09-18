<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Observer\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\UpdateManagement;

/**
 * Class CustomerRepositorySaveAfter
 *
 * @package Aheadworks\CreditLimit\Observer\Customer
 */
class CustomerRepositorySaveAfter implements ObserverInterface
{
    /**
     * @var UpdateManagement
     */
    private $updateManagement;

    /**
     * @param UpdateManagement $updateManagement
     */
    public function __construct(
        UpdateManagement $updateManagement
    ) {
        $this->updateManagement = $updateManagement;
    }

    /**
     * Check customer group after customer saving
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /* @var $newCustomer CustomerInterface */
        $newCustomer = $observer->getCustomerDataObject();
        /* @var $oldCustomer CustomerInterface */
        $oldCustomer = $observer->getOrigCustomerDataObject();
        $oldCustomerGroup = $oldCustomer ? $oldCustomer->getGroupId() : 0;
        if ($oldCustomerGroup != $newCustomer->getGroupId()) {
            $this->updateManagement->updateCreditLimitOnGroupChange(
                $oldCustomerGroup,
                $newCustomer
            );
        }
    }
}
