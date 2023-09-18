<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\ViewModel;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class PermissionProvider
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\ViewModel
 */
class PermissionProvider implements ArgumentInterface
{
    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @param RequisitionListPermission $listPermission
     */
    public function __construct(
        RequisitionListPermission $listPermission
    ) {
        $this->listPermission = $listPermission;
    }

    /**
     * Check whether list is editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return $this->listPermission->isEditable();
    }
}
