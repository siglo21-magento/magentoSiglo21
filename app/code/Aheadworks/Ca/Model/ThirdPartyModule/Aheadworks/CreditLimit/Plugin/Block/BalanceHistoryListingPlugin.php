<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;

/**
 * Class BalanceHistoryListingPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block
 */
class BalanceHistoryListingPlugin
{
    /**
     * @var CreditLimitManagement
     */
    private $creditLimitManagement;

    /**
     * @param CreditLimitManagement $creditLimitManagement
     */
    public function __construct(
        CreditLimitManagement $creditLimitManagement
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
    }

    /**
     * Apply additional params credit balance listing
     *
     * @param \Aheadworks\CreditLimit\Block\Customer\BalanceHistory\Listing $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetComponentParams($subject, $result)
    {
        if (!isset($result[CompanyUserInterface::CUSTOMER_ID])) {
            return $result;
        }

        $rootCustomer = $this->creditLimitManagement->getRootUserByCustomerId(
            $result[CompanyUserInterface::CUSTOMER_ID]
        );
        if ($rootCustomer) {
            $companyId = $rootCustomer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
            unset($result[CompanyUserInterface::CUSTOMER_ID]);
            $result[CompanyUserInterface::COMPANY_ID] = $companyId;
        }

        return $result;
    }

    /**
     * Check whether balance history grid is visible
     *
     * @param \Aheadworks\CreditLimit\Block\Customer\BalanceHistory\Listing $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        if (!$this->creditLimitManagement->isAvailableTransactions()) {
            $resultHtml = '';
        }

        return $resultHtml;
    }
}
