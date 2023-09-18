<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Account;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Link
 *
 * @package Aheadworks\Ctq\Block\Account
 */
class Link extends LinkCurrent
{
    /**
     * @var BuyerPermissionManagementInterface
     */
    private $buyerPermissionManagement;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CustomerSession $customerSession
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        CustomerSession $customerSession,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->customerSession = $customerSession;
        $this->buyerPermissionManagement = $buyerPermissionManagement;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _toHtml()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $storeId = $this->_storeManager->getStore()->getId();
        if (!$this->buyerPermissionManagement->isAllowQuotesForCustomer($customerId, $storeId)) {
            return '';
        }

        return parent::_toHtml();
    }
}
