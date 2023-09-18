<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterfaceFactory;
use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\History\Action\Type as ActionType;
use Aheadworks\Ctq\Model\Source\History\Action\Status as ActionStatus;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Class ShippingAddressBuilder
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
class ShippingAddressBuilder implements BuilderInterface
{
    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @var array
     */
    private $fieldsToCheck = [
        AddressInterface::KEY_EMAIL,
        AddressInterface::KEY_COUNTRY_ID,
        AddressInterface::KEY_REGION_ID,
        AddressInterface::KEY_REGION_CODE,
        AddressInterface::KEY_REGION,
        AddressInterface::KEY_STREET,
        AddressInterface::KEY_COMPANY,
        AddressInterface::KEY_TELEPHONE,
        AddressInterface::KEY_FAX,
        AddressInterface::KEY_POSTCODE,
        AddressInterface::KEY_CITY,
        AddressInterface::KEY_FIRSTNAME,
        AddressInterface::KEY_LASTNAME,
        AddressInterface::KEY_MIDDLENAME,
        AddressInterface::KEY_PREFIX,
        AddressInterface::KEY_SUFFIX,
        AddressInterface::KEY_VAT_ID
    ];

    /**
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param array $fieldsToCheck
     */
    public function __construct(
        HistoryActionInterfaceFactory $historyActionFactory,
        $fieldsToCheck = []
    ) {
        $this->historyActionFactory = $historyActionFactory;
        $this->fieldsToCheck = array_merge($this->fieldsToCheck, $fieldsToCheck);
    }

    /**
     * {@inheritdoc}
     */
    public function build($quote)
    {
        $historyActions = [];
        if ($quote->getOrigData(QuoteInterface::ID) === null) {
            return $historyActions;
        }

        /** @var QuoteCartInterface $oldCart */
        $oldCart = $quote->getOrigData(QuoteInterface::CART);
        $oldAddress = $oldCart ? $oldCart->getShippingAddress() : [];
        $newAddress = $quote->getCart()->getShippingAddress();

        $addressHistoryActions = [];
        foreach ($newAddress as $fieldKey => $fieldValue) {
            if (!in_array($fieldKey, $this->fieldsToCheck)) {
                continue;
            }

            $oldFieldValue = isset($oldAddress[$fieldKey]) ? $oldAddress[$fieldKey] : '';
            if ($fieldValue != $oldFieldValue) {
                /** @var HistoryActionInterface $historyAction */
                $historyAction = $this->historyActionFactory->create();
                $historyAction
                    ->setType(ActionType::SHIPPING_ADDRESS_ATTRIBUTE)
                    ->setName($fieldKey)
                    ->setStatus(ActionStatus::UPDATED)
                    ->setOldValue($oldFieldValue)
                    ->setValue($fieldValue);

                $addressHistoryActions[] = $historyAction;
            }
        }

        if ($addressHistoryActions) {
            /** @var HistoryActionInterface $historyAction */
            $historyAction = $this->historyActionFactory->create();
            $historyAction
                ->setType(ActionType::SHIPPING_ADDRESS)
                ->setStatus(ActionStatus::UPDATED)
                ->setActions($addressHistoryActions);
            $historyActions[] = $historyAction;
        }

        $newShippingMethod = $newAddress['shipping_method'];
        $oldShippingMethod = isset($oldAddress['shipping_method']) ? $oldAddress['shipping_method'] : '';
        $newShippingMethodDesc = $newAddress['shipping_description'];
        $oldShippingMethodDesc = isset($oldAddress['shipping_description']) ? $oldAddress['shipping_description'] : '';
        if ($newShippingMethod != $oldShippingMethod) {
            /** @var HistoryActionInterface $historyAction */
            $historyAction = $this->historyActionFactory->create();
            $historyAction
                ->setType(ActionType::SHIPPING_METHOD)
                ->setStatus(ActionStatus::UPDATED)
                ->setOldValue($oldShippingMethodDesc)
                ->setValue($newShippingMethodDesc);
            $historyActions[] = $historyAction;
        }

        return $historyActions;
    }
}
