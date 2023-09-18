<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;

/**
 * Class CustomerGroupConfig
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
class CustomerGroupConfig extends AbstractDb
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_cl_customer_group_credit_limit';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, 'id');
    }

    /**
     * Save configuration values
     *
     * @param array $configValue
     * @param int $websiteId
     * @throws \Exception
     * @return $this
     */
    public function saveConfigValue($configValue, $websiteId)
    {
        $configValue[SummaryInterface::WEBSITE_ID] =
            $configValue[SummaryInterface::WEBSITE_ID] ?? $websiteId;

        $connection = $this->transactionManager->start($this->getConnection());
        try {
            $this->removeConfigValue($configValue['customer_group_id'], $websiteId);
            $connection->insertOnDuplicate(
                $this->getMainTable(),
                [$configValue],
                []
            );
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Load configuration values
     *
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function loadConfigValue($websiteId)
    {
        if (!$this->hasConfigValueForWebsite($websiteId)) {
            $websiteId = 0;
        }

        return $this->loadData($websiteId);
    }

    /**
     * Remove configuration values
     *
     * @param int $customerGroupId
     * @param int $websiteId
     * @return $this
     * @throws LocalizedException
     */
    public function removeConfigValue($customerGroupId, $websiteId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                SummaryInterface::WEBSITE_ID . ' = ?' => $websiteId,
                'customer_group_id = ?' => $customerGroupId
            ]
        );
        return $this;
    }

    /**
     * If there are config value for specified website
     *
     * @param int $websiteId
     * @return bool
     * @throws LocalizedException
     */
    public function hasConfigValueForWebsite($websiteId)
    {
        $data = $this->loadData($websiteId);
        return count($data) > 0;
    }

    /**
     * If there are config value for specified website and customer group
     *
     * @param int $customerGroupId
     * @param int $websiteId
     * @return bool
     * @throws LocalizedException
     */
    public function hasConfigValueForCustomerGroup($customerGroupId, $websiteId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where(SummaryInterface::WEBSITE_ID . ' =:website_id')
            ->where('customer_group_id =:customer_group_id');
        $data = $this->getConnection()->fetchAll(
            $select,
            [
                ':website_id' => $websiteId,
                ':customer_group_id' => $customerGroupId
            ]
        );

        return count($data) > 0;
    }

    /**
     * Load data for website
     *
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function loadData($websiteId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where(SummaryInterface::WEBSITE_ID . ' =:website_id');
        return $this->getConnection()->fetchAll($select, [':website_id' => $websiteId]);
    }
}
