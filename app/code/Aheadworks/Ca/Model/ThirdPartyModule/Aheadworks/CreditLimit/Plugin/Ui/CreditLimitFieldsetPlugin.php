<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Ui;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Ui\CompanyNotificationApplier;

/**
 * Class CreditLimitFieldsetPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Ui
 */
class CreditLimitFieldsetPlugin
{
    /**
     * @var CompanyNotificationApplier
     */
    private $notificationApplier;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyNotificationApplier $notificationApplier
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyNotificationApplier $notificationApplier,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->notificationApplier = $notificationApplier;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Prepare credit limit form for message
     *
     * @param \Aheadworks\CreditLimit\Ui\Component\Form\Customer\CreditLimitFieldset $subject
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforePrepare($subject)
    {
        $customerId = $subject->getContext()->getRequestParam('id');
        if ($customerId) {
            $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);
            if ($rootCustomer) {
                $this->notificationApplier->hideCreditLimitComponents($subject);
                $companyId = $rootCustomer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
                $this->notificationApplier->applyNotification($subject, $companyId);
            }
        }

        return null;
    }
}
