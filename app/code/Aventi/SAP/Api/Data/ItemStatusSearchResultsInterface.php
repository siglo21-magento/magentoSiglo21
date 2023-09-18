<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Api\Data;

interface ItemStatusSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ItemStatus list.
     * @return \Aventi\SAP\Api\Data\ItemStatusInterface[]
     */
    public function getItems();

    /**
     * Set line_sap list.
     * @param \Aventi\SAP\Api\Data\ItemStatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
