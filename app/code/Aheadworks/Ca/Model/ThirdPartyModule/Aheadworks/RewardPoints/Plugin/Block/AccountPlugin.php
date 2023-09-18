<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Block;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;

/**
 * Class AccountPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Block
 */
class AccountPlugin
{
    /**
     * @var RewardPointsManagement
     */
    private $rewardPointsManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param RewardPointsManagement $rewardPointsManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        RewardPointsManagement $rewardPointsManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->rewardPointsManagement = $rewardPointsManagement;
    }

    /**
     * Retrieve customer transaction grid
     *
     * @param \Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance\Account $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetTransactionHtml($subject, $proceed)
    {
        $html = '';
        if ($this->rewardPointsManagement->isAvailableTransactions()) {
            $html = $proceed();
        }

        return $html;
    }

    /**
     * Add message before grid with transactions
     *
     * @param \Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance\Account $subject
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
     * @param \Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance\Account $block
     * @return string
     */
    private function getMessage($block)
    {
        $message = '';
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser) {
            $balance = $block->getFormattedCustomerBalanceCurrency();
            $text = __('You can spend <strong>%1</strong> on your next order', $balance);
            $message = '<span class="base">' . $text . '</span>';
        }

        return $message;
    }
}
