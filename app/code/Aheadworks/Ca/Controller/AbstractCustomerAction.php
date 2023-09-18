<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ResponseInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class AbstractCustomerAction
 * @package Aheadworks\Ca\Controller
 */
abstract class AbstractCustomerAction extends Action
{
    /**
     * Check if entity belongs to customer
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = false;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        } elseif (static::IS_ENTITY_BELONGS_TO_CUSTOMER
            && !$this->isEntityBelongsToCustomer()
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    /**
     * Check if entity belongs to current customer
     *
     * @return bool
     * @throws NotFoundException
     */
    abstract protected function isEntityBelongsToCustomer();

    /**
     * Retrieve entity id by request
     *
     * @return int
     */
    protected function getEntityIdByRequest()
    {
        return (int)$this->getRequest()->getParam('id');
    }

    /**
     * Check is forward action
     *
     * @param array $actions
     * @return bool
     */
    protected function isForwardAction($actions)
    {
        $beforeForward = $this->getRequest()->getBeforeForwardInfo();
        $isForwardAction = isset($beforeForward['action_name']) && in_array($beforeForward['action_name'], $actions);

        return $isForwardAction;
    }

    /**
     * Retrieve current company user
     *
     * @return CustomerInterface
     */
    protected function getCurrentCompanyUser()
    {
        $currentCompanyUser = $this->customerSession->getCustomer()->getDataModel();
        // acl will not provide access to this controller to a user
        // who does not belong to the company
        return $currentCompanyUser;
    }

    /**
     * Retrieve current company id
     *
     * @return int
     */
    protected function getCurrentCompanyId()
    {
        return $this->getCurrentCompanyUser()->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
    }

    /**
     * Set back link
     *
     * @param $resultPage
     * @param $backUrl
     * @return void
     */
    protected function setBackLink($resultPage, $backUrl = null)
    {
        $backUrl = $backUrl ? : $this->_redirect->getRefererUrl();
        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($backUrl);
        }
    }
}
