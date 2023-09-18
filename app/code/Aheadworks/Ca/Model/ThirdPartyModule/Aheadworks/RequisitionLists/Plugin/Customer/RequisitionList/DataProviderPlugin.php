<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Customer\RequisitionList;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListProvider;
use Aheadworks\RequisitionLists\ViewModel\Customer\RequisitionList\DataProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class DataProviderPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Customer\RequisitionList
 */
class DataProviderPlugin implements ArgumentInterface
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
     * Get JS Layout and insert needed values for modal form fields
     *
     * @param DataProvider $subject
     * @param string $result
     * @return string
     * @throws \Zend_Json_Exception
     */
    public function afterGetPreparedJsLayout($subject, $result)
    {
        if ($this->listPermission->isEditable() && $this->listPermission->isCustomerHasCompanyPermissions()) {
            $jsLayout = \Zend_Json::decode($result, true);

            if (isset($jsLayout['components']['awRequisitionListParent']['children']
                ['awRequisitionList']['children']['awRequisitionListForm']['children']
                ['fieldset']['children'])
            ) {
                $formFields = &$jsLayout['components']['awRequisitionListParent']['children']
                ['awRequisitionList']['children']['awRequisitionListForm']['children']
                ['fieldset']['children'];

                $formFields['shared']['visible'] = true;
                $formFields['shared']['default'] = $this->getCurrentRequisitionListIsShared();
            }

            return \Zend_Json::encode($jsLayout);
        } else {
            return $result;
        }
    }

    /**
     * Retrieve current requisition list is shared
     *
     * @return bool
     */
    private function getCurrentRequisitionListIsShared()
    {
        if ($list = $this->listProvider->getList()) {
            return $list->getShared() ? '1' : '0';
        }

        return false;
    }
}
