<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\AsyncUpdater\Job\Processor;

use Aheadworks\CreditLimit\Model\AsyncUpdater\Job\ProcessorInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface as ParamsInterface;
use Aheadworks\CreditLimit\Model\Customer\SummaryLoader;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\CustomerGroupConfig as CustomerGroupConfigResource;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CreditLimitUpdater
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job\Processor
 */
class CreditLimitUpdater implements ProcessorInterface
{
    /**
     * @var SummaryLoader
     */
    private $summaryLoader;

    /**
     * @var CreditLimitManagementInterface
     */
    private $creditLimitManagement;

    /**
     * @var CustomerGroupConfigResource
     */
    private $customerGroupConfigResource;

    /**
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param SummaryLoader $summaryLoader
     * @param CustomerGroupConfigResource $customerGroupConfigResource
     */
    public function __construct(
        CreditLimitManagementInterface $creditLimitManagement,
        SummaryLoader $summaryLoader,
        CustomerGroupConfigResource $customerGroupConfigResource
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
        $this->summaryLoader = $summaryLoader;
        $this->customerGroupConfigResource = $customerGroupConfigResource;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function process($configuration)
    {
        if (!$configuration[SummaryInterface::CREDIT_LIMIT]) {
            $this->customerGroupConfigResource->removeConfigValue(
                $configuration['customer_group_id'],
                $configuration[SummaryInterface::WEBSITE_ID]
            );
        } else {
            $this->customerGroupConfigResource->saveConfigValue(
                $configuration,
                $configuration[SummaryInterface::WEBSITE_ID]
            );
        }

        $summaryList = $this->summaryLoader->loadByCustomerGroupId($configuration['customer_group_id']);
        foreach ($summaryList as $summary) {
            if ($summary->getCompanyId()
                || $this->isWebsiteValueConfigured($configuration, $summary->getWebsiteId())
            ) {
                continue;
            }

            $this->creditLimitManagement->updateDefaultCreditLimit(
                $summary->getCustomerId(),
                $configuration[SummaryInterface::CREDIT_LIMIT]
            );
        }

        return true;
    }

    /**
     * Is website value configured
     *
     * @param array $configuration
     * @param int $customerWebsiteId
     * @return bool
     * @throws LocalizedException
     */
    private function isWebsiteValueConfigured($configuration, $customerWebsiteId)
    {
        if ($configuration[SummaryInterface::WEBSITE_ID] == 0) {
            return $this->customerGroupConfigResource->hasConfigValueForCustomerGroup(
                $configuration['customer_group_id'],
                $customerWebsiteId
            );
        }

        return false;
    }
}
