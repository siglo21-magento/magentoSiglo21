<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Form
 *
 * @package Aheadworks\Ca\Block
 * @method \Aheadworks\Ca\ViewModel\Form getFormViewModel()
 */
class Form extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Ca::form.phtml';

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $dataProvider = $this->getFormViewModel()->getDataProvider();
        $this->jsLayout = $dataProvider->modifyMeta($this->jsLayout);

        $id = $this->_request->getParam($dataProvider->getRequestFieldName(), null);
        $data = $dataProvider->getData();
        if ($id && isset($data[$id])) {
            $this->jsLayout['components'][$dataProvider->getName()]['data'] = $data[$id];
        }
        return json_encode($this->jsLayout);
    }
}
