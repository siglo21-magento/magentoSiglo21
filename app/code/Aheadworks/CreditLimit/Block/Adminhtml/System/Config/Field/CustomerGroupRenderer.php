<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Block\Adminhtml\System\Config\Field;

use Aheadworks\CreditLimit\Model\Source\Customer\Group as GroupSource;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class CustomerGroupRenderer
 *
 * @package Aheadworks\CreditLimit\Block\Adminhtml\System\Config\Field
 */
class CustomerGroupRenderer extends Select
{
    /**
     * @var GroupSource
     */
    private $groupSource;

    /**
     * @param Context $context
     * @param GroupSource $groupSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        GroupSource $groupSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->groupSource = $groupSource;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->groupSource->toOptionArray());
        }

        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set html id for input element
     *
     * @param string $id
     * @return $this
     */
    public function setInputId($id)
    {
        return $this->setId($id);
    }
}
