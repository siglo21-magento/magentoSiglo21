<?php

namespace Aventi\SAP\Block\Customer;

use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\DefaultPathInterface;


class Link extends LinkCurrent
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        CustomerSession $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $customerId = $this->customerSession->getCustomerId();

        return parent::_toHtml();
    }
}
