<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Block;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\StoreCreditManagement;

/**
 * Class AccountPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Block
 */
class AccountPlugin
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var StoreCreditManagement
     */
    private $storeCreditManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param StoreCreditManagement $storeCreditManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        StoreCreditManagement $storeCreditManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->storeCreditManagement = $storeCreditManagement;
    }

    /**
     * Retrieve customer transaction grid
     *
     * @param \Aheadworks\StoreCredit\Block\Customer\StoreCreditBalance\Account $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetTransactionHtml($subject, $proceed)
    {
        $html = '';
        if ($this->storeCreditManagement->isAvailableTransactions()) {
            $html = $proceed();
        }

        return $html;
    }

    /**
     * Add message before grid with transactions
     *
     * @param \Aheadworks\StoreCredit\Block\Customer\StoreCreditBalance\Account $subject
     * @param string $result
     * @return string
     */
    public function afterGetTransactionHtml($subject, $result)
    {
        return $this->getMessage($subject) . $result;
    }

    /**
     * Get message about limit in account
     *
     * @param \Aheadworks\StoreCredit\Block\Customer\StoreCreditBalance\Account $block
     * @return string
     */
    private function getMessage($block)
    {
        $message = '';
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser) {
            $balance = $block->getCustomerStoreCreditBalanceFormatted();
            $text = __('You can spend <strong>%1</strong> on your next order', $balance);
            $message = '<span class="base">' . $text . '</span>';
        }

        return $message;
    }
}
