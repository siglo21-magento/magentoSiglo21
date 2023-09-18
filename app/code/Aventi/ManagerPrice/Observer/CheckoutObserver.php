<?php

namespace Aventi\ManagerPrice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;

class CheckoutObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;

    /**
     * @var LayoutInterface
     */
    private $_layout;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Aventi\ManagerPrice\Helper\Data $helper
    ) {
        $this->_layout = $layout;
        $this->_helper = $helper;
    }

    /**
       * @inheritDoc
       */
    public function execute(Observer $observer)
    {

    }
}
