<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Ui\Component\Listing;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListProvider;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option\AbstractOption;
use Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option\MoveTo;
use Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\MassAction\Option\CopyTo;
use Magento\Framework\UrlInterface;

/**
 * Class MassActionPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Ui\Component\Listing
 */
class MassActionPlugin
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
     * @var UrlInterface
     */
    private $url;

    /**
     * @param RequisitionListPermission $listPermission
     * @param RequisitionListProvider $listProvider
     * @param UrlInterface $url
     */
    public function __construct(
        RequisitionListPermission $listPermission,
        RequisitionListProvider $listProvider,
        UrlInterface $url
    ) {
        $this->listPermission = $listPermission;
        $this->listProvider = $listProvider;
        $this->url = $url;
    }

    /**
     * Get action options for sub accounts if allowed and add lists to list if user is administrator
     *
     * @param AbstractOption $subject
     * @param array $result
     * @return array|null
     */
    public function afterJsonSerialize($subject, $result)
    {
        if ($this->listPermission->isEditable()) {
            if ($this->listPermission->isCustomerHasRootPermissions()) {
                $companyLists = $this->listProvider->getCompanyLists();
                $preparedLists = [];
                foreach ($companyLists as $list) {
                    $preparedLists[] = [
                        'id' => $list->getListId(),
                        'type' => 'list_' . $list->getListId(),
                        'label' => $list->getName(),
                        'url' => $this->prepareUrl($subject, $list)
                    ];
                }
                $result = array_merge($result, array_values($preparedLists));
            }

            return $result;
        }

        return null;
    }

    /**
     * Prepare URLs for lists actions
     *
     * @param AbstractOption $subject
     * @param RequisitionListInterface $list
     * @return string
     */
    private function prepareUrl($subject, $list)
    {
        if ($subject instanceof MoveTo) {
            return $this->url->getUrl(
                'aw_rl/rlist/moveItem',
                [
                    'list_id' => $this->listProvider->getListId(),
                    'move_to_list' => $list->getListId()
                ]
            );
        } elseif ($subject instanceof CopyTo) {
            return $this->url->getUrl(
                'aw_rl/rlist/copyItem',
                [
                    'list_id' => $this->listProvider->getListId(),
                    'copy_to_list' => $list->getListId()
                ]
            );
        }

        return '';
    }
}
