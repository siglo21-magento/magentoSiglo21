<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Role;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\RoleRepositoryInterface;

/**
 * Class Edit
 * @package Aheadworks\Ca\Controller\Role
 */
class Edit extends AbstractRoleAction
{
    /**
     * {@inheritdoc}
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param RoleRepositoryInterface $roleRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        RoleRepositoryInterface $roleRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $customerSession, $roleRepository);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $roleId = $this->getEntityIdByRequest();
        if ($roleId) {
            $role = $this->getEntity();
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($roleId ? __('Edit Role %1', $role->getName()) : __('New Role'));

        return $resultPage;
    }
}
