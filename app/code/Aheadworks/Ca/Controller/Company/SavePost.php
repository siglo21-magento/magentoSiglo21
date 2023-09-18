<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Company;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Aheadworks\Ca\Ui\DataProvider\FormDataProvider;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SavePost
 * @package Aheadworks\Ca\Controller\Company
 */
class SavePost extends Action
{
    /**
     * @var SellerCompanyManagementInterface
     */
    private $sellerCompanyService;

    /**
     * @var CustomerExtractor
     */
    private $customerExtractor;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ExtensionAttributesBuilder
     */
    private $extensionAttributesManagement;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @param Context $context
     * @param SellerCompanyManagementInterface $sellerCompanyService
     * @param CustomerExtractor $customerExtractor
     * @param DataPersistorInterface $dataPersistor
     * @param ExtensionAttributesBuilder $extensionAttributesManagement
     * @param DataProcessor $dataProcessor
     * @param AuthorizationManagementInterface $authorizationManagement
     */
    public function __construct(
        Context $context,
        SellerCompanyManagementInterface $sellerCompanyService,
        CustomerExtractor $customerExtractor,
        DataPersistorInterface $dataPersistor,
        ExtensionAttributesBuilder $extensionAttributesManagement,
        DataProcessor $dataProcessor,
        AuthorizationManagementInterface $authorizationManagement
    ) {
        parent::__construct($context);
        $this->sellerCompanyService = $sellerCompanyService;
        $this->customerExtractor = $customerExtractor;
        $this->dataPersistor = $dataPersistor;
        $this->extensionAttributesManagement = $extensionAttributesManagement;
        $this->dataProcessor = $dataProcessor;
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
        if ($data = $request->getPostValue()) {
            try {
                $company = $this->dataProcessor->prepareCompany($request);
                $customer = $this->dataProcessor->prepareCustomer($request);
                $companyId = $company->getId();

                if (!$customer->getId() && !$companyId) {
                    $this->sellerCompanyService->createCompany($company, $customer);
                    $this->messageManager->addSuccessMessage(
                        __('The company information was accepted for moderation. We will contact you shortly.')
                    );
                    $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                    return $resultRedirect->setPath('customer/account/index');
                } else {
                    if (!$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::companies_edit')) {
                        return $resultRedirect->setUrl($this->_url->getUrl('noroute'));
                    }

                    $this->sellerCompanyService->updateCompany($company, $customer);
                    $this->messageManager->addSuccessMessage(
                        __('The company was successfully saved.')
                    );
                    $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                    return $resultRedirect->setPath('aw_ca/company/index');
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong when saving company.')
                );
            }
            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            if ($companyId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $companyId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/create', ['_current' => true]);
        }

        return $resultRedirect->setPath('*/*/create', ['_current' => true]);
    }
}
