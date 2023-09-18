<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Role;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\RoleRepositoryInterface;

/**
 * Class Create
 * @package Aheadworks\Ca\Controller\Role
 */
class Create extends AbstractRoleAction
{
    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param RoleRepositoryInterface $roleRepository
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        RoleRepositoryInterface $roleRepository,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $customerSession, $roleRepository);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Forward to edit
     *
     * @return Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
