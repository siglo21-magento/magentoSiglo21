<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel\Company\Grid;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\ResourceModel\Company\Collection as CompanyCollection;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Aheadworks\Ca\Model\ResourceModel\Company\Grid
 */
class Collection extends CompanyCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var array
     */
    private $additionalColumns = [
        'is_root' => 'company_user.is_root',
        'customer_phone' => 'company_user.telephone',
        'customer_name' => "CONCAT(customer.firstname, ' ', customer.lastname)",
        'customer_email' => 'customer.email',
        'customer_group' => 'customer.group_id',
        'customer_job' => 'company_user.job_title'
    ];

    /**
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AbstractDb $eventPrefix
     * @param mixed $eventObject
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb $resource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $eventPrefix,
        $eventObject,
        $model = Document::class,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->setModel($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @return $this|CompanyCollection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinInner(
            ['company_user' => $this->getTable(CompanyUser::MAIN_TABLE_NAME)],
            'company_user.'. CompanyUserInterface::COMPANY_ID .' = main_table.' . CompanyInterface::ID
                . ' AND company_user.is_root = 1',
            []
        );
        $this->getSelect()->joinInner(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = company_user.' . CompanyUserInterface::CUSTOMER_ID,
            []
        );
        $this->getSelect()->columns($this->additionalColumns);

        foreach ($this->additionalColumns as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'customer_name') {
            $whereCondition = [
                $this->_translateCondition('customer.firstname', $condition),
                $this->_translateCondition('customer.lastname', $condition)
            ];
            $this->getSelect()->where(new \Zend_Db_Expr(implode(' OR ', $whereCondition)));

            return $this;
        }

        if (isset($this->additionalColumns[$field])) {
            $this->addFilterToMap($field, $this->additionalColumns[$field]);
        } else {
            $this->createMainTableFieldAlias($field);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Create main field alias
     *
     * @param $field
     */
    private function createMainTableFieldAlias($field)
    {
        if (is_array($field)) {
            foreach ($field as $filterItem) {
                $this->addFilterToMap($filterItem, 'main_table.' . $filterItem);
            }
        } else {
            $this->addFilterToMap($field, 'main_table.' . $field);
        }
    }
}
