<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Role
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class Role extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ca_role';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RoleInterface::ID);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|RoleInterface $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->isDefault()) {
            $this->getConnection()
                ->update(
                    $this->getMainTable(),
                    [RoleInterface::IS_DEFAULT => 0],
                    RoleInterface::COMPANY_ID . ' = ' . $object->getCompanyId()
                    . ' And '
                    . RoleInterface::ID . ' != ' . $object->getId()
                );
        }
        return parent::_afterSave($object);
    }
}
