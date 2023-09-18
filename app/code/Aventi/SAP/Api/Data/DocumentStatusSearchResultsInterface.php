<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Api\Data;

interface DocumentStatusSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get DocumentStatus list.
     * @return \Aventi\SAP\Api\Data\DocumentStatusInterface[]
     */
    public function getItems();

    /**
     * Set sap list.
     * @param \Aventi\SAP\Api\Data\DocumentStatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

