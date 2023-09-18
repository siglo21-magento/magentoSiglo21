<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Block\User;

use Aheadworks\Ca\Block\Grid;

/**
 * Class UserList
 * @package Aheadworks\Ca\Block\User
 * @method \Aheadworks\Ca\ViewModel\User\UserList getUserListViewModel()
 * @method \Aheadworks\Ca\ViewModel\User\User getUserViewModel()
 */
class UserList extends Grid
{
    /**
     * {@inheritdoc}
     */
    protected function getPagerName()
    {
        return 'aw_ca.customer.user.list.pager';
    }

    /**
     * {@inheritdoc}
     */
    protected function getListViewModel()
    {
        return $this->getUserListViewModel();
    }
}
