<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Adminhtml\Company;

use Aheadworks\Ca\Model\ResourceModel\Company\Collection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassChangeStatus
 * @package Aheadworks\Ca\Controller\Adminhtml\Company
 */
class MassChangeStatus extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction(Collection $collection)
    {
        $status = $this->getRequest()->getParam('status');
        $updatedRecords = 0;
        foreach ($collection->getAllIds() as $companyId) {
            try {
                $company = $this->companyRepository->get($companyId);
                if ($company->getStatus() == $status) {
                    continue;
                }
                $this->companyManagement->changeStatus($companyId, $status);
                $updatedRecords++;
            } catch (LocalizedException $e) {
            }
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
