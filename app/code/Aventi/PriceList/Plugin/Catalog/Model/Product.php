<?php

namespace Aventi\PriceList\Plugin\Catalog\Model;

use Aventi\PriceList\Model\Session;
use Magento\Framework\App\State;

class Product
{
    /**
     * @var \Aventi\SAP\Helper\Attribute
     */
    private $attributeData;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;
    /**
     * @var PriceList
     */
    private $helper;
    /**
     * @var State
     */
    private $state;
    /**
     * @var Session
     */
    private $session;

    public function __construct(
        \Aventi\SAP\Helper\Attribute $attributeData,
        \Aventi\PriceList\Helper\PriceList $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Psr\Log\LoggerInterface $logger,
        State $state,
        Session $session
    ) {
        $this->attributeData = $attributeData;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->groupRepository = $groupRepository;
        $this->helper = $helper;
        $this->state = $state;
        $this->session = $session;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $customerGroup = null;
        if ($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $customer = $this->helper->getCustomer($this->session->getCustomerAdminhtml());
            if ($customer) {
                $customerGroup = $this->helper->getGroup($customer->getGroupId());
            }
        } else {
            $groupId = $this->customerSession->getCustomer()->getGroupId();
            $customerGroup = $this->helper->getGroup($groupId);
        }
        if ($customerGroup) {
            $code = $this->attributeData->formatGroupLabel($customerGroup->getCode());
            $price = $this->helper->getPriceBySkuAndList($subject->getSku(), $code);
            if (!is_null($price)) {
                $result = (float) $price;
            }
        }
        return $result;
    }
}
