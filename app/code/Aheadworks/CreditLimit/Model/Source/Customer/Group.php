<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Convert\DataObject;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;

/**
 * Class Group
 *
 * @package Aheadworks\CreditLimit\Model\Source\Customer
 */
class Group implements OptionSourceInterface
{
    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param GroupManagementInterface $groupManagement
     * @param DataObject $objectConverter
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        GroupManagementInterface $groupManagement,
        DataObject $objectConverter,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->groupManagement = $groupManagement;
        $this->objectConverter = $objectConverter;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $sortOrderData = $this->sortOrderBuilder->getData();

        $groups = $this->groupManagement->getLoggedInGroups();

        // fix for magento bug with SortOrderBuilder
        if (!empty($sortOrderData)) {
            $this->sortOrderBuilder
                ->setField($sortOrderData[SortOrder::FIELD])
                ->setDirection($sortOrderData[SortOrder::DIRECTION]);
        }

        return $this->objectConverter->toOptionArray($groups, GroupInterface::ID, GroupInterface::CODE);
    }
}
