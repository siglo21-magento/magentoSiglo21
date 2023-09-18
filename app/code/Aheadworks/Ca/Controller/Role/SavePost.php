<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Role;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\Data\RoleInterfaceFactory;
use Aheadworks\Ca\Api\RoleManagementInterface;
use Aheadworks\Ca\Ui\DataProvider\FormDataProvider;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;

/**
 * Class SavePost
 * @package Aheadworks\Ca\Controller\Role
 */
class SavePost extends AbstractRoleAction
{
    /**
     * @var RoleInterfaceFactory
     */
    private $roleFactory;

    /**
     * @var RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param RoleRepositoryInterface $roleRepository
     * @param RoleInterfaceFactory $roleFactory
     * @param RoleManagementInterface $roleManagement
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectHelper $dataObjectHelper
     * @param AuthorizationManagementInterface $authorizationManagement
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        RoleRepositoryInterface $roleRepository,
        RoleInterfaceFactory $roleFactory,
        RoleManagementInterface $roleManagement,
        DataPersistorInterface $dataPersistor,
        DataObjectHelper $dataObjectHelper,
        AuthorizationManagementInterface $authorizationManagement
    ) {
        parent::__construct($context, $customerSession, $roleRepository);
        $this->roleFactory = $roleFactory;
        $this->roleManagement = $roleManagement;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $roleId = $this->getRequest()->getParam('id');
                if (($roleId
                        && !$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_roles_edit'))
                    || (!$roleId
                        && !$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_roles_add_new'))
                ) {
                    return $resultRedirect->setUrl($this->_url->getUrl('noroute'));
                }

                $this->performSave($data);
                $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('The Role was saved successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while save the role.')
                );
            }
            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            $roleId = isset($data['id']) ? $data['id'] : false;
            if ($roleId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $roleId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/create', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return RoleInterface
     * @throws LocalizedException
     */
    private function performSave($data)
    {
        if (isset($data['permissions'])) {
            $postedResources = $data['permissions'];
            unset($data['permissions']);
        } else {
            $postedResources = [];
        }

        /** @var RoleInterface $roleObject */
        $roleObject = $this->roleFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $roleObject,
            $data,
            RoleInterface::class
        );

        $roleObject->setCompanyId(
            $this->getCurrentCompanyId()
        );

        return $this->roleManagement->saveRole($roleObject, $postedResources);
    }
}
