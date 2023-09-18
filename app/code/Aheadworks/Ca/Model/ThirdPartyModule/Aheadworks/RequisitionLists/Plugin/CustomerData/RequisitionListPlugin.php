<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\CustomerData;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListProvider;
use Aheadworks\RequisitionLists\CustomerData\RequisitionList;

/**
 * Class RequisitionListPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\CustomerData
 */
class RequisitionListPlugin
{
    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @var RequisitionListProvider
     */
    private $listProvider;

    /**
     * @param RequisitionListPermission $listPermission
     * @param RequisitionListProvider $listProvider
     */
    public function __construct(
        RequisitionListPermission $listPermission,
        RequisitionListProvider $listProvider
    ) {
        $this->listPermission = $listPermission;
        $this->listProvider = $listProvider;
    }

    /**
     * Add other customers lists to list if user is administrator
     *
     * @param RequisitionList $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData($subject, $result)
    {
        if ($this->listPermission->isCustomerHasRootPermissions()) {
            $companyLists = $this->listProvider->getCompanyLists();

            $preparedLists = [];
            foreach ($companyLists as $list) {
                $preparedLists[] = [
                    'list_id' => $list->getListId(),
                    'name' => $list->getName()
                ];
            }
            $result['lists'] = array_merge($result['lists'], $preparedLists);
        }

        return $result;
    }
}
