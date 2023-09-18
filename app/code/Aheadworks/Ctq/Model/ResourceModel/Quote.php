<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\ResourceModel;

use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class Quote
 * @package Aheadworks\Ctq\Model\ResourceModel
 */
class Quote extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ctq_quote';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, QuoteInterface::ID);
    }

    /**
     * Get quote identifier by cart id
     *
     * @param int $cartId
     * @return int|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdByCartId($cartId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('cart_id = :cart_id');

        return $connection->fetchOne($select, ['cart_id' => $cartId]);
    }
}
