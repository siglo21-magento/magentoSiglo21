<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\User;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Ca\Controller\User
 */
class ChangeStatus extends AbstractUserAction
{
    /**
     * Check if entity belongs to customer
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $customerSession, $customerRepository);
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->getEntityIdByRequest();

        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $status = $this->getRequest()->getParam('activate');

                if ($this->getCurrentCompanyUser()->getId() == $customerId) {
                    throw new LocalizedException(__('The user status can\'t be changed.'));
                }
                if ($customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot()) {
                    throw new LocalizedException(__('The admin user status can\'t be changed.'));
                }
                if (!isset($status)) {
                    throw new LocalizedException(__('Requested user status is not specified.'));
                }

                $customer->getExtensionAttributes()->getAwCaCompanyUser()->setIsActivated($status);
                $this->customerRepository->save($customer);

                $this->messageManager->addSuccessMessage(
                    __('The user status has been changed.')
                );
            } catch (NoSuchEntityException $exception) {
                throw new NotFoundException(__('Page not found.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
