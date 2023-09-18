<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Adminhtml\User;

use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class CheckEmail
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\User
 */
class CheckEmail extends BackendAction
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
     * @param Context $context
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        Context $context,
        CompanyUserManagementInterface $companyUserManagement,
        DataObjectProcessor $dataObjectProcessor
    ) {
        parent::__construct($context);
        $this->companyUserManagement = $companyUserManagement;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Check email action
     *
     * @return Json
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        if (!$email) {
            $result = [
                'error' => __('Email is required'),
            ];
        } else {
            try {
                $websiteId = $this->getRequest()->getParam('website_id');
                $availabilityResult = $this->companyUserManagement->isEmailAvailable($email, $websiteId);
                $result = $this->dataObjectProcessor->buildOutputDataArray(
                    $availabilityResult,
                    EmailAvailabilityResultInterface::class
                );
            } catch (\Exception $e) {
                $result = [
                    'error' => $e->getMessage(),
                    'errorcode' => $e->getCode()
                ];
            }
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
