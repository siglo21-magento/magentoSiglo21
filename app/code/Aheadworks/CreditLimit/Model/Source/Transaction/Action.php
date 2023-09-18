<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Source\Transaction;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Action
 *
 * @package Aheadworks\CreditLimit\Model\Source\Transaction
 */
class Action implements OptionSourceInterface
{
    /**#@+
     * Transaction action values
     */
    const CREDIT_LIMIT_ASSIGNED = 'credit_limit_assigned';
    const CREDIT_LIMIT_CHANGED = 'credit_limit_changed';
    const CREDIT_BALANCE_UPDATED = 'credit_balance_updated';
    const ORDER_PURCHASED = 'order_purchased';
    const ORDER_CANCELED = 'order_canceled';
    const CREDIT_MEMO_REFUNDED = 'credit_memo_refunded';
    /**#@-*/

    /**
     * @var array
     */
    private $actionsWithCommentPlaceholders = [];

    /**
     * @var array
     */
    private $actionsToUpdateCreditBalance = [];

    /**
     * @var array
     */
    private $actionsToUpdateCreditLimit = [];

    /**
     * @var array
     */
    private $actionsToReimburseBalance = [];

    /**
     * @param array $actionsWithCommentPlaceholders
     * @param array $actionsToUpdateCreditBalance
     * @param array $actionsToUpdateCreditLimit
     * @param array $actionsToReimburseBalance
     */
    public function __construct(
        array $actionsWithCommentPlaceholders = [],
        array $actionsToUpdateCreditBalance = [],
        array $actionsToUpdateCreditLimit = [],
        array $actionsToReimburseBalance = []
    ) {
        $this->actionsWithCommentPlaceholders = $actionsWithCommentPlaceholders;
        $this->actionsToUpdateCreditBalance = $actionsToUpdateCreditBalance;
        $this->actionsToUpdateCreditLimit = $actionsToUpdateCreditLimit;
        $this->actionsToReimburseBalance = $actionsToReimburseBalance;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CREDIT_LIMIT_ASSIGNED,
                'label' => __('Assigned')
            ],
            [
                'value' => self::CREDIT_LIMIT_CHANGED,
                'label' => __('Changed')
            ],
            [
                'value' => self::CREDIT_BALANCE_UPDATED,
                'label' => __('Updated')
            ],
            [
                'value' => self::ORDER_PURCHASED,
                'label' => __('Purchased')
            ],
            [
                'value' => self::ORDER_CANCELED,
                'label' => __('Canceled')
            ],
            [
                'value' => self::CREDIT_MEMO_REFUNDED,
                'label' => __('Refunded')
            ]
        ];
    }

    /**
     * Get all transaction actions
     *
     * @return array
     */
    public function getAllActions()
    {
        $optionList = $this->toOptionArray();
        $actions = [];
        foreach ($optionList as $option) {
            $actions[] = $option['value'];
        }

        return $actions;
    }

    /**
     * Get action label
     *
     * @param string $action
     * @return string
     */
    public function getActionLabel($action)
    {
        $optionList = $this->toOptionArray();
        $actionLabel = '';
        foreach ($optionList as $option) {
            if ($option['value'] == $action) {
                $actionLabel = $option['label'];
                break;
            }
        }

        return $actionLabel;
    }

    /**
     * Prepare list of actions which contains placeholders for rendering
     *
     * @return array
     */
    public function getActionsWithCommentPlaceholders()
    {
        return $this->actionsWithCommentPlaceholders;
    }

    /**
     * Prepare list of actions to update credit balance
     *
     * @return array
     */
    public function getActionsToUpdateCreditBalance()
    {
        return $this->actionsToUpdateCreditBalance;
    }

    /**
     * Prepare list of actions to update credit limit
     *
     * @return array
     */
    public function getActionsToUpdateCreditLimit()
    {
        return $this->actionsToUpdateCreditLimit;
    }

    /**
     * Prepare list of actions to reimburse balance;
     *
     * @return array
     */
    public function getActionsToReimburseBalance()
    {
        return $this->actionsToReimburseBalance;
    }
}
