<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;

/**
 * Class TransactionServicePlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin
 */
class TransactionServicePlugin
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
     * Change transaction params before proceed
     *
     * @param \Aheadworks\CreditLimit\Api\TransactionManagementInterface $subject
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface $params
     * @return array
     */
    public function beforeCreateTransaction($subject, $params)
    {
        $rootCustomer = $this->creditLimitManagement->getRootUserByCustomerId($params->getCustomerId());
        if ($rootCustomer) {
            $companyId = $rootCustomer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
            $params->setCustomerId($rootCustomer->getId());
            $params->setCompanyId($companyId);
        }

        return [$params];
    }
}
