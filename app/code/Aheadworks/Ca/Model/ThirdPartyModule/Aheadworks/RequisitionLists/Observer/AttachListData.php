<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Observer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\JoinProcessor;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AttachListData
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Observer
 */
class AttachListData implements ObserverInterface
{
    /**
     * @var JoinProcessor
     */
    private $joinProcessor;

    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @param JoinProcessor $joinProcessor
     * @param RequisitionListPermission $listPermission
     */
    public function __construct(
        JoinProcessor $joinProcessor,
        RequisitionListPermission $listPermission
    ) {
        $this->joinProcessor = $joinProcessor;
        $this->listPermission = $listPermission;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if ($this->listPermission->isCustomerHasCompanyPermissions()) {
            /** @var Collection $collection */
            $collection = $observer->getAwRlCollectionObject();
            $this->joinProcessor->joinColumns($collection);
        }
    }
}
