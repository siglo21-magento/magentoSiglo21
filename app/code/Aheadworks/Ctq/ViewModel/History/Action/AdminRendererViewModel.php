<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\History\Action;

use Aheadworks\Ctq\Model\Magento\ModuleUser\UserRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class AdminRendererViewModel
 * @package Aheadworks\Ctq\ViewModel\History\Action
 */
class AdminRendererViewModel implements ArgumentInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve admin name by id
     *
     * @param int $adminId
     * @return string
     */
    public function getAdminName($adminId)
    {
        try {
            $user = $this->userRepository->getById($adminId);
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = 'Undefined';
        }
        return $name;
    }
}
