<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Plugin\Controller;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class FrontActionPlugin
 * @package Aheadworks\Ca\Plugin\Controller
 */
class FrontActionPlugin
{
    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var SellerCompanyManagementInterface
     */
    private $companyManagement;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CustomerSession $customerSession
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param SellerCompanyManagementInterface $companyManagement
     * @param Context $context
     */
    public function __construct(
        AuthorizationManagementInterface $authorizationManagement,
        CustomerSession $customerSession,
        CompanyUserManagementInterface $companyUserManagement,
        SellerCompanyManagementInterface $companyManagement,
        Context $context
    ) {
        $this->messageManager = $context->getMessageManager();
        $this->authorizationManagement = $authorizationManagement;
        $this->customerSession = $customerSession;
        $this->companyUserManagement = $companyUserManagement;
        $this->companyManagement = $companyManagement;
    }

    /**
     * Check company blocking and current user permission
     *
     * @param ActionInterface $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws NotFoundException
     */
    public function aroundDispatch(
        ActionInterface $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {
        try {
            $this->checkCompanyBlocking();
            $this->checkUserPermission($request);
        } catch (AuthorizationException $e) {
            $request->initForward();
            $request->setModuleName('aw_ca');
            $request->setControllerName('forbidden');
            $request->setActionName('index');
            $request->setDispatched(false);
        }

        return $proceed($request);
    }

    /**
     * Check company blocking
     *
     * @return void
     */
    protected function checkCompanyBlocking()
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser) {
            $companyUser = $currentUser->getExtensionAttributes()->getAwCaCompanyUser();
            if ($this->companyManagement->isBlockedCompany($companyUser->getCompanyId())
                || !$companyUser->getIsActivated()
            ) {
                $this->customerSession->logout();
                $this->messageManager->addErrorMessage(
                    __('Your account has been blocked. You may contact the seller for more information.')
                );
            }
        }
    }

    /**
     * Check current user permission
     *
     * @param RequestInterface $request
     * @return void
     * @throws AuthorizationException
     * @throws NotFoundException
     */
    protected function checkUserPermission(RequestInterface $request)
    {
        if ($request->isAjax()
            || ($request->getModuleName() == 'aw_ca'
                && $request->getControllerName() == 'forbidden')
            || ($request->getModuleName() == 'cms'
                && $request->getControllerName() == 'noroute')
        ) {
            return;
        }

        $beforeForward = $request->getBeforeForwardInfo();
        $params = [$request->getModuleName(), $request->getControllerName(), $request->getActionName()];
        if (!empty($beforeForward)) {
            $params = [
                $beforeForward['module_name'],
                $beforeForward['controller_name'],
                $beforeForward['action_name']
            ];
        }
        $path = implode('/', $params);
        if (!$this->authorizationManagement->isAllowed($path)) {
            $companyUser = $this->companyUserManagement->getCurrentUser();
            if ($companyUser) {
                throw new AuthorizationException(__('Forbidden.'));
            } else {
                throw new NotFoundException(__('Page not found.'));
            }
        }
    }
}
