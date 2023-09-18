<?php
/**
 * Quick order by parte equipos
 * Copyright (C) 2018
 *
 * This file is part of Aventi/QuickOrder.
 *
 * Aventi/QuickOrder is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aventi\QuickOrder\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
    }

    /**
     * Generate form key
     *
     * @method
     * date 31/05/19/11:43 AM
     *
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
