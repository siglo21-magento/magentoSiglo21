<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Company;
use Aheadworks\Ca\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ca\Model\ResourceModel\Company as CompanyResource;

/**
 * Class CompanyCollection
 * @package Aheadworks\Ca\Model\ResourceModel\Company
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Company::class, CompanyResource::class);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap(CompanyInterface::EMAIL, 'main_table.' . CompanyInterface::EMAIL);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            CompanyResource::COMPANY_PAYMENTS_TABLE_NAME,
            CompanyInterface::ID,
            'company_id',
            'payment_name',
            CompanyInterface::ALLOWED_PAYMENT_METHODS
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            CompanyResource::COMPANY_PAYMENTS_TABLE_NAME,
            CompanyInterface::ID,
            'company_id',
            CompanyInterface::ALLOWED_PAYMENT_METHODS,
            'payment_name'
        );
        parent::_renderFiltersBefore();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, [CompanyInterface::ALLOWED_PAYMENT_METHODS])) {
            $this->addFilter($field, $condition, 'public');
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add website filter to collection
     *
     * @return $this
     */
    public function addWebsiteFilter()
    {
        $this->getSelect()
            ->joinLeft(
                ['awcacu' => $this->getTable('aw_ca_company_user')],
                'awcacu.company_id = main_table.id',
                []
            )->joinLeft(
                ['customer' => $this->getTable('customer_entity')],
                'customer.entity_id = awcacu.customer_id',
                ['website_id']
            )->where(CompanyUserInterface::IS_ROOT . ' = ?', 1);

        return $this;
    }
}
