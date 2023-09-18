<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\CreditLimit\Model\Customer\Layout\Processor\TotalList as TotalListProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class TotalList
 *
 * @package Aheadworks\CreditLimit\Block\Customer
 */
class TotalList extends Template
{
    /**
     * @var TotalListProcessor
     */
    private $totalListProcessor;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param TotalListProcessor $totalListProcessor
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        TotalListProcessor $totalListProcessor,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->totalListProcessor = $totalListProcessor;
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
    }

    /**
     * Prepare JS layout of block
     *
     * @throws LocalizedException
     */
    public function getJsLayout()
    {
        $customerId = $this->getCustomerId();
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $this->jsLayout = $this->totalListProcessor->process($this->jsLayout, $customerId, $websiteId);
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Retrieve customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
}
