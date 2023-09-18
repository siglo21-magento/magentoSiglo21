<?php
/**
 * Add file comment to orders
 * Copyright (C) 2018  
 * 
 * This file is part of Aventi/OrderComment.
 * 
 * Aventi/OrderComment is free software: you can redistribute it and/or modify
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

namespace Aventi\OrderComment\Block\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aventi\OrderComment\Model\Data\OrderComment;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;


class Comment extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
    }

    /**
     * Get Order
     *
     * @return array|null
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Get Order Comment
     *
     * @return string
     */
    public function getOrderComment()
    {
        return trim($this->getOrder()->getData(OrderComment::COMMENT_FIELD_NAME));
    }


    /**
     * Check if has order comment
     *
     * @return bool
     */
    public function hasOrderComment()
    {
        return strlen($this->getOrderComment()) > 0;
    }
}
