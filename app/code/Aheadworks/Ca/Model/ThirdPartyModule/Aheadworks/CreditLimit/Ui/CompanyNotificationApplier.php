<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Ui;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company\CustomerFormNotification;

/**
 * Class CompanyNotificationApplier
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Ui
 */
class CompanyNotificationApplier
{
    /**
     * Component name for message rendering
     */
    const MESSAGE_COMPONENT_NAME = 'message-container';

    /**
     * @var CustomerFormNotification
     */
    private $customerFormNotification;

    /**
     * @var array
     */
    private $componentListToHide = [
        'aw_credit_limit_top_totals' => true,
        'credit_limit' => true,
        'credit_limit_comment_to_admin' => true,
        'update_balance_heading' => true,
        'amount_currency' => true,
        'amount' => true,
        'po_number' => true,
        'balance_comment_to_customer' => true,
        'balance_comment_to_admin' => true
    ];

    /**
     * @param CustomerFormNotification $customerFormNotification
     * @param array $componentListToHide
     */
    public function __construct(
        CustomerFormNotification $customerFormNotification,
        array $componentListToHide = []
    ) {
        $this->customerFormNotification = $customerFormNotification;
        $this->componentListToHide = array_merge($this->componentListToHide, $componentListToHide);
    }

    /**
     * Hide credit limit components
     *
     * @param UiComponentInterface $component
     */
    public function hideCreditLimitComponents(UiComponentInterface $component)
    {
        $childComponents = $component->getChildComponents();
        foreach ($childComponents as $child) {
            if (isset($this->componentListToHide[$child->getName()])) {
                $config = $child->getData('config');
                $config['componentDisabled'] = $this->componentListToHide[$child->getName()];
                $child->setData('config', $config);
            }
        }
    }

    /**
     * Apply notification
     *
     * @param UiComponentInterface $component
     * @param int $companyId
     * @throws NoSuchEntityException
     */
    public function applyNotification($component, $companyId)
    {
        $childComponents = $component->getChildComponents();
        if (isset($childComponents[self::MESSAGE_COMPONENT_NAME])) {
            $messageContainer = $childComponents[self::MESSAGE_COMPONENT_NAME];
            $config = $messageContainer->getData('config');
            $config['componentDisabled'] = false;
            $config['message'] = $this->customerFormNotification->getHtmlMessage($companyId);
            $messageContainer->setData('config', $config);
        }
    }
}
