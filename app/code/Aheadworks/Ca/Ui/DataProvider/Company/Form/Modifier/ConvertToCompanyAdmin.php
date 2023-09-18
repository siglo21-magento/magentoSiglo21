<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class ConvertToCompanyAdmin
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier
 */
class ConvertToCompanyAdmin implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $data['isEnabledConverter'] = !(isset($data['id']) && $data['id'] != '');
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
