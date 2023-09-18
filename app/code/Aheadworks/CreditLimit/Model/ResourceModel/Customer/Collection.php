<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel\Customer;

use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Data\Collection\EntityFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Eav\Model\ResourceModel\Helper as ResourceHelper;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\DataObject\Copy\Config as CopyConfig;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\CustomerExtendedSql;

/**
 * Class Collection
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel\Customer
 */
class Collection extends CustomerCollection
{
    /**
     * @var CustomerExtendedSql
     */
    protected $customerExtendedSql;

    /**
     * @var array
     */
    protected $extendedStaticFields = [
        SummaryInterface::SUMMARY_ID,
        SummaryInterface::CUSTOMER_ID,
        SummaryInterface::CREDIT_LIMIT,
        SummaryInterface::CURRENCY,
        SummaryInterface::IS_CUSTOM_CREDIT_LIMIT,
        SummaryInterface::CREDIT_BALANCE,
        SummaryInterface::LAST_PAYMENT_DATE,
        SummaryInterface::CREDIT_AVAILABLE,
        'group_id',
        'website_id',
        'customer_email',
        'customer_name'
    ];

    /**
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param EavConfig $eavConfig
     * @param ResourceConnection $resource
     * @param EavEntityFactory $eavEntityFactory
     * @param ResourceHelper $resourceHelper
     * @param UniversalFactory $universalFactory
     * @param Snapshot $entitySnapshot
     * @param CopyConfig $fieldsetConfig
     * @param CustomerExtendedSql $customerExtendedSql
     * @param AdapterInterface|null $connection
     * @param string $modelName
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EavConfig $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        ResourceHelper $resourceHelper,
        UniversalFactory $universalFactory,
        Snapshot $entitySnapshot,
        CopyConfig $fieldsetConfig,
        CustomerExtendedSql $customerExtendedSql,
        AdapterInterface $connection = null,
        $modelName = self::CUSTOMER_MODEL_NAME
    ) {
        $this->customerExtendedSql = $customerExtendedSql;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $entitySnapshot,
            $fieldsetConfig,
            $connection,
            $modelName
        );
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $select = $this->getSelect();
        $select->reset(Select::COLUMNS);

        $this->customerExtendedSql->applyColumns($select);
        $this->customerExtendedSql->applyJoins($select);

        $connection = $this->getConnection();
        $havingCondition = [
            $connection->quoteInto(SummaryInterface::CREDIT_LIMIT . ' >= ?', 0),
            $connection->quoteInto(SummaryInterface::CREDIT_BALANCE . ' != ?', 0)
        ];

        $select->having(implode(' OR ', $havingCondition));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (in_array($attribute, $this->extendedStaticFields)) {
            $this->getSelect()->order($attribute . ' ' . $dir);
            return $this;
        } else {
            return parent::addAttributeToSort($attribute, $dir);
        }
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        if (is_array($attribute)) {
            return $this->addFieldToFilterAsArray($attribute);
        }

        return $this->addFieldToFilterAsString($attribute, $condition);
    }

    /**
     * Add field to filter as array
     *
     * This filtering is used by search criteria builder
     *
     * @param array $attributeList
     * @return $this
     */
    protected function addFieldToFilterAsArray($attributeList)
    {
        $sqlArr = [];
        foreach ($attributeList as $attributeData) {
            if (!in_array($attributeData['attribute'], $this->extendedStaticFields)) {
                continue;
            }
            $sqlArr[] = $this->_translateCondition($attributeData['attribute'], $attributeData);
        }
        if (!empty($sqlArr)) {
            $resultCondition = '(' . implode(') OR (', $sqlArr) . ')';
            $this->getSelect()->having($resultCondition, null, Select::TYPE_CONDITION);
        }

        return $this;
    }

    /**
     * Add field to filter as string
     *
     * This filtering is used by listing component
     *
     * @param string $attribute
     * @param array|string|null $condition
     * @return $this|CustomerCollection|AbstractDb
     */
    protected function addFieldToFilterAsString($attribute, $condition)
    {
        $resultCondition = $this->_translateCondition($attribute, $condition);
        if (in_array($attribute, $this->extendedStaticFields)) {
            $this->getSelect()->having($resultCondition, null, Select::TYPE_CONDITION);
        } else {
            $this->_select->where($resultCondition, null, Select::TYPE_CONDITION);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::COLUMNS);
        $this->customerExtendedSql->applyColumns($countSelect);
        $this->customerExtendedSql->applyJoins($countSelect);

        $resultSelect = $this->getConnection()->select()->from($countSelect, 'COUNT(*)');

        return $resultSelect;
    }

    /**
     * @inheritdoc
     */
    protected function beforeAddLoadedItem(DataObject $item)
    {
        return $item;
    }
}
