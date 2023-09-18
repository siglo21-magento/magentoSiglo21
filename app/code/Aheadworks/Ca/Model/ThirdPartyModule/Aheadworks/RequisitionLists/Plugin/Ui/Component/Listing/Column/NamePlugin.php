<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Ui\Component\Listing\Column;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\Column\Name;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class NamePlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Ui\Component\Listing\Column
 */
class NamePlugin
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
     * Add info about editing lists by sub accounts
     *
     * @param Name $subject
     * @param array $result
     * @param array $data
     * @return array
     */
    public function afterPrepareDataSource($subject, $result, $data)
    {
        if (isset($result['data']['items'])) {
            foreach ($result['data']['items'] as & $item) {
                try {
                    $item['is_can_be_edited'] = $this->listPermission->isEditable();
                } catch (NoSuchEntityException $e) {
                    $item['is_can_be_edited'] = false;
                }
            }
        }

        return $result;
    }
}
