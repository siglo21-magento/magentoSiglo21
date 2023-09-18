<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Block\Adminhtml\Customer;

use Magento\Backend\Block\Template;

/**
 * Class TabActivator
 *
 * @method string|null getParamToTrigger()
 * @method string|null getParamValue()
 * @method string|null getTabId()
 * @package Aheadworks\CreditLimit\Block\Adminhtml\Customer
 */
class TabActivator extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_CreditLimit::customer/tab-activator.phtml';

    /**
     * Check is active
     *
     * @return bool
     */
    public function isActive()
    {
        $param = $this->_request->getParam($this->getParamToTrigger());
        if ($param == $this->getParamValue()) {
            return true;
        }

        return false;
    }
}
