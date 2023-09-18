<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Adminhtml\Company;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\Ca\Ui\DataProvider\FormDataProvider;
use Aheadworks\Ca\Controller\Company\DataProcessor;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company\Updater;

/**
 * Class Save
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\Company
 */
class Save extends Action
{
    /**
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var SellerCompanyManagementInterface
     */
    private $sellerCompanyService;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Updater
     */
    private $creditLimitUpdater;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param DataProcessor $dataProcessor
     * @param SellerCompanyManagementInterface $sellerCompanyService
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Updater $creditLimitUpdater
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        DataProcessor $dataProcessor,
        SellerCompanyManagementInterface $sellerCompanyService,
        CompanyUserManagementInterface $companyUserManagement,
        CustomerRepositoryInterface $customerRepository,
        Updater $creditLimitUpdater
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->dataProcessor = $dataProcessor;
        $this->sellerCompanyService = $sellerCompanyService;
        $this->companyUserManagement = $companyUserManagement;
        $this->customerRepository = $customerRepository;
        $this->creditLimitUpdater = $creditLimitUpdater;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $requestData = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($requestData) {
            $companyId = isset($requestData['company']['id']) ? (int)$requestData['company']['id'] : null;
            try {
                $customer = $this->dataProcessor->prepareCustomer($this->getRequest());
                $company = $this->dataProcessor->prepareCompany($this->getRequest());

                if (!$company->getId()) {
                    $customer = $this->updateCustomerIfAvailableConvertToCompanyAdmin($customer);
                    $this->sellerCompanyService->createCompany($company, $customer);
                } else {
                    $this->sellerCompanyService->updateCompany($company, $customer);
                }

                $this->creditLimitUpdater->updateCreditLimit(
                    $company->getId(),
                    $this->getRequest()->getParam(Updater::CREDIT_LIMIT_PARAM)
                );

                $companyId = $company->getId();
                $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('The company was successfully saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $companyId, '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the company.')
                );
            }

            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $requestData);
            if ($companyId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $companyId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Update customer if available convert to company admin
     *
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws LocalizedException
     */
    private function updateCustomerIfAvailableConvertToCompanyAdmin($customer)
    {
        $websiteId = $this->getRequest()->getParam('website_id');
        if ($this->companyUserManagement->isAvailableConvertToCompanyAdmin($customer->getEmail(), $websiteId)) {
            $existingCustomer = $this->customerRepository->get($customer->getEmail(), $websiteId);
            $existingCustomer
                ->setFirstname($customer->getFirstname())
                ->setLastname($customer->getLastname());
            return $existingCustomer;
        }
        return $customer;
    }
}
