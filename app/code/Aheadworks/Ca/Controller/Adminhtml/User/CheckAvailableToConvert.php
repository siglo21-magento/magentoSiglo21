<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Adminhtml\User;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class GetCustomerByEmail
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\User
 */
class CheckAvailableToConvert extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Context $context
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        CompanyUserManagementInterface $companyUserManagement,
        DataObjectProcessor $dataObjectProcessor,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->companyUserManagement = $companyUserManagement;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check email action
     *
     * @return Json
     */
    public function execute()
    {
        $result = [
            'isAvailable' => false,
            'isAvailableForConvert' => false,
            'customer' => null
        ];

        $email = $this->getRequest()->getParam('email');
        if (!$email) {
            return $this->error(__('Email is required'));
        }

        try {
            $websiteId = $this->getRequest()->getParam('website_id');

            $availableResult = $this->companyUserManagement->isEmailAvailable($email, $websiteId);
            $isAvailableToConvert = $this->companyUserManagement->isAvailableConvertToCompanyAdmin(
                $email,
                $websiteId
            );

            $result['isAvailable'] = $availableResult->isAvailableForCustomer();
            $result['isAvailableForConvert'] = $isAvailableToConvert;

            if ($isAvailableToConvert) {
                $customer = $this->customerRepository->get($email, $websiteId);
                $result['customer'] = $this->dataObjectProcessor->buildOutputDataArray(
                    $customer,
                    CustomerInterface::class
                );
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->json($result);
    }

    /**
     * Create json result object
     *
     * @param array $data
     * @return Json
     */
    private function json(array $data)
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($data);
    }

    /**
     * Create error json result
     *
     * @param $message
     * @return Json
     */
    private function error($message)
    {
        return $this->json([
            'error' => $message
        ]);
    }
}
