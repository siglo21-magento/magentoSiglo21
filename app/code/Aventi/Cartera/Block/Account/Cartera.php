<?php

namespace Aventi\Cartera\Block\Account;

use Aheadworks\CreditLimit\Model\Customer\Layout\Processor\TotalList as TotalListProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Cartera extends \Magento\Framework\View\Element\Template
{
    /**
     * @var TotalListProcessor
     */
    private $totalListProcessor;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_helperView;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Helper\View $helperView
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        TotalListProcessor $totalListProcessor,
        \Magento\Customer\Helper\View $helperView,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->totalListProcessor = $totalListProcessor;
        $this->_helperView = $helperView;
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
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Retrieve customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->currentCustomer->getCustomerId();
    }

    /**
     * Get the full name of a customer
     *
     * @return string full name
     */
    public function getName()
    {
        return $this->_helperView->getCustomerName($this->getCustomer());
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }
}
