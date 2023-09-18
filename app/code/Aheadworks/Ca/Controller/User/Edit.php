<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\User;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\Page;

/**
 * Class Edit
 * @package Aheadworks\Ca\Controller\User
 */
class Edit extends AbstractUserAction
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
     * @throws LocalizedException
     */
    public function execute()
    {
        $customerId = $this->getEntityIdByRequest();
        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (NoSuchEntityException $exception) {
                throw new NotFoundException(__('Page not found.'));
            } catch (LocalizedException $e) {
                throw new NotFoundException(__('Page not found.'));
            }
            $this->getRequest()->setParams(['entity_id' => $customerId]);
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        /** @noinspection PhpUndefinedVariableInspection */
        $resultPage->getConfig()->getTitle()->set(
            $customerId
                ? __('Edit User %1', $customer->getFirstname() . ' ' . $customer->getLastname())
                : __('New User')
        );

        return $resultPage;
    }
}
