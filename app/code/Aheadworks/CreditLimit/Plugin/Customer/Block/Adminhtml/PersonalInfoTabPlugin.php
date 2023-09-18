<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Plugin\Customer\Block\Adminhtml;

use Aheadworks\CreditLimit\Block\Adminhtml\Customer\TabActivator;
use Aheadworks\CreditLimit\Block\Adminhtml\Customer\TabActivatorFactory;
use Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo;

/**
 * Class PersonalInfoTabPlugin
 *
 * @package Aheadworks\CreditLimit\Plugin\Customer\Block\Adminhtml
 */
class PersonalInfoTabPlugin
{
    /**
     * Request param to trigger tab auto activation
     */
    const PARAM_TO_TRIGGER = 'tab';

    /**
     * Request param value
     */
    const PARAM_VALUE = 'aw_cl';

    /**
     * Tab ID to activate
     */
    const TAB_ID = 'tab_aw_credit_limit_data';

    /**
     * @var TabActivatorFactory
     */
    private $tabActivatorFactory;

    /**
     * @param TabActivatorFactory $tabActivatorFactory
     */
    public function __construct(
        TabActivatorFactory $tabActivatorFactory
    ) {
        $this->tabActivatorFactory = $tabActivatorFactory;
    }

    /**
     * Render tab activator to jump to credit limit tab
     *
     * @param PersonalInfo $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        /** @var TabActivator $tabActivator */
        $tabActivator = $this->tabActivatorFactory->create(
            [
                'data' => [
                    'param_to_trigger' => self::PARAM_TO_TRIGGER,
                    'param_value' => self::PARAM_VALUE,
                    'tab_id' => self::TAB_ID
                ]
            ]
        );
        $resultHtml .= $tabActivator->toHtml();

        return $resultHtml;
    }
}
