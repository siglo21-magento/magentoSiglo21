<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\Component\Listing;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Action
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\Component\Listing
 */
class Action extends Column
{
    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param RequisitionListPermission $listPermission
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        RequisitionListPermission $listPermission,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->listPermission = $listPermission;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->listPermission->isEditable()) {
            $config = $this->getData('config');
            $config['actionDisable'] = true;
            $this->setData('config', $config);
        }
    }
}
