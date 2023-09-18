<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\User;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Aheadworks\Ca\Ui\DataProvider\FormDataProvider;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;

/**
 * Class SavePost
 * @package Aheadworks\Ca\Controller\User
 */
class SavePost extends AbstractUserAction
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerExtractor
     */
    private $customerExtractor;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ExtensionAttributesBuilder
     */
    private $companyUserExtAttributesBuilder;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**     
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerExtractor $customerExtractor
     * @param DataObjectHelper $dataObjectHelper
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param DataPersistorInterface $dataPersistor
     * @param ExtensionAttributesBuilder $extensionAttributesManagement
     * @param CompanyRepositoryInterface $companyRepository
     * @param AuthorizationManagementInterface $authorizationManagement
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper,
        CompanyUserManagementInterface $companyUserManagement,
        DataPersistorInterface $dataPersistor,
        ExtensionAttributesBuilder $extensionAttributesManagement,
        CompanyRepositoryInterface $companyRepository,
        AuthorizationManagementInterface $authorizationManagement       
    ) {
        parent::__construct($context, $customerSession, $customerRepository);
        $this->customerRepository = $customerRepository;
        $this->customerExtractor = $customerExtractor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->companyUserManagement = $companyUserManagement;
        $this->dataPersistor = $dataPersistor;
        $this->companyUserExtAttributesBuilder = $extensionAttributesManagement;
        $this->companyRepository = $companyRepository;
        $this->authorizationManagement = $authorizationManagement;
        $this->customerSession = $customerSession;               
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $userId = $this->getRequest()->getParam('entity_id');
                if (($userId
                        && !$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_users_edit'))
                    || (!$userId
                        && !$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_users_add_new'))
                ) {
                    return $resultRedirect->setUrl($this->_url->getUrl('noroute'));
                }

                if ($userId) {
                    $companyUser = $this->customerRepository->getById($userId);
                } else {
                    $companyUser = $this->customerExtractor->extract(
                        'customer_account_create',
                        $this->getRequest()
                    );
                }

                $this->performSave($companyUser, $data);
                                
                $this->customerSession->setCustomerCreated($companyUser->getEmail());
                $this->messageManager->addSuccessMessage(__('The user was successfully saved.'));
                $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);

                return $resultRedirect->setPath('*/*/index');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong when saving user.')
                );
            }

            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
    }

    /**
     * Perform save company user
     *
     * @param CustomerInterface $companyUser
     * @param array $data
     * @throws LocalizedException
     */
    private function performSave(CustomerInterface $companyUser, array $data)
    {
        $this->companyUserExtAttributesBuilder->setAwCompanyUserIfNotIsset($companyUser);

        $this->dataObjectHelper->populateWithArray(
            $companyUser,
            $data,
            CustomerInterface::class
        );

        $currentCompany = $this->getCurrentCompanyUser()->getExtensionAttributes()->getAwCaCompanyUser();
        $company = $this->companyRepository->get($currentCompany->getCompanyId());
        $companyUser->getExtensionAttributes()->getAwCaCompanyUser()
            ->setCompanyGroupId($currentCompany->getCompanyGroupId())
            ->setCompanyId($this->getCurrentCompanyId());
        $companyUser->setGroupId($company->getCustomerGroupId());

        $this->companyUserManagement->saveUser($companyUser);
    }    
}
