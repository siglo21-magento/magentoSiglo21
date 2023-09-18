<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Block\Role;

use Aheadworks\Ca\Block\Grid;

/**
 * Class RoleList
 * @package Aheadworks\Ca\Block\Role
 * @method \Aheadworks\Ca\ViewModel\Role\RoleList getRoleListViewModel()
 * @method \Aheadworks\Ca\ViewModel\Role\Role getRoleViewModel()
 */
class RoleList extends Grid
{
    /**
     * {@inheritdoc}
     */
    protected function getPagerName()
    {
        return 'aw_ca.role.list.pager';
    }

    /**
     * {@inheritdoc}
     */
    protected function getListViewModel()
    {
        return $this->getRoleListViewModel();
    }
}
