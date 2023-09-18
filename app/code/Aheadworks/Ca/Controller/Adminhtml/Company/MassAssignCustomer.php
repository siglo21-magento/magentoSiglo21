<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Adminhtml\Company;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MassAssignCustomer
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\Company
 */
class MassAssignCustomer extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CustomerCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CustomerCollectionFactory $collectionFactory
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CustomerCollectionFactory $collectionFactory,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            /** @var CustomerCollection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('customer/index');
        }
    }

    /**
     * Perform mass action
     *
     * @param CustomerCollection $collection
     * @return ResultRedirect
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function massAction(CustomerCollection $collection)
    {
        $companyId = $this->getRequest()->getParam('id');
        $updatedRecords = 0;
        /** @var CustomerInterface $customer */
        foreach ($collection->getAllIds() as $customerId) {
            $isAssigned = $this->companyUserManagement->assignUserToCompany($customerId, $companyId);
            if ($isAssigned) {
                $updatedRecords++;
            }
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('customer/index');
    }
}
