<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Source\Admin;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Ca\Model\Source\Admin\User\Loader as UserLoader;

/**
 * Class User
 *
 * @package Aheadworks\Ca\Model\Source\Admin
 */
class User implements OptionSourceInterface
{
    /**
     * @var UserLoader
     */
    private $userLoader;

    /**
     * @param UserLoader $userLoader
     */
    public function __construct(
        UserLoader $userLoader
    ) {
        $this->userLoader = $userLoader;
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $users = $this->userLoader->load();
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[] = [
                'value' => $user->getUserId(),
                'label' => $user->getUserFullname()
            ];
        }
        return $userOptions;
    }
}
