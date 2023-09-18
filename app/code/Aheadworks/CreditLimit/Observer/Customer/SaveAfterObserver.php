<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider as CreditLimitDataProvider;
use Aheadworks\CreditLimit\Model\Customer\Backend\BalanceUpdater;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface as ParamsInterface;

/**
 * Class SaveAfterObserver
 *
 * @package Aheadworks\CreditLimit\Observer\Customer
 */
class SaveAfterObserver implements ObserverInterface
{
    /**
     * @var BalanceUpdater
     */
    private $balanceUpdater;

    /**
     * @param BalanceUpdater $balanceUpdater
     */
    public function __construct(
        BalanceUpdater $balanceUpdater
    ) {
        $this->balanceUpdater = $balanceUpdater;
    }

    /**
     * Customer credit limit update after save
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /* @var $request RequestInterface */
        $request = $observer->getRequest();
        $creditLimitData = $request->getParam(CreditLimitDataProvider::CREDIT_LIMIT_DATA_SCOPE);
        $customerId = $request->getParam(ParamsInterface::CUSTOMER_ID);
        $defaultData = $request->getParam('use_default');

        $this->balanceUpdater->updateCreditLimit($customerId, $creditLimitData, $defaultData);
        $this->balanceUpdater->updateCreditBalance($customerId, $creditLimitData);
    }
}
