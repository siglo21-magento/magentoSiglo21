<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel\Role;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Aheadworks\Ca\Model\ResourceModel\Role as RoleResource;
use Aheadworks\Ca\Model\Role;
use Aheadworks\Ca\Model\Role\EntityProcessor;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory as FrameworkEntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Aheadworks\Ca\Model\ResourceModel\Role
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * @var EntityProcessor
     */
    private $entityProcessor;

    /**
     * @param FrameworkEntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param EntityProcessor $entityProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        FrameworkEntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EntityProcessor $entityProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->entityProcessor = $entityProcessor;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Role::class, RoleResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        /** @var Role $item */
        foreach ($this as $item) {
            $this->entityProcessor->prepareDataAfterLoad($item);
        }
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['company_user' => $this->getTable(CompanyUser::MAIN_TABLE_NAME)],
            'company_user.' . CompanyUserInterface::COMPANY_ROLE_ID . ' = main_table.' . RoleInterface::ID,
            [
                'count_users' => 'COUNT(company_user.' . CompanyUserInterface::CUSTOMER_ID . ')'
            ]
        );
        $this->getSelect()->group('main_table.id');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $this->createMainTableFieldAlias($field);
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

    /**
     * @inheritDoc
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->resetJoinLeft();

        return $select;
    }
}
