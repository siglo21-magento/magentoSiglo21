<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface OwnerInterface
 * @api
 */
interface OwnerInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const OWNER_TYPE = 'owner_type';
    const OWNER_NAME = 'owner_name';
    const OWNER_ID = 'owner_id';
    /**#@-*/

    /**
     * Get owner type
     *
     * @return string
     */
    public function getOwnerType();

    /**
     * Set owner type
     *
     * @param string $ownerType
     * @return $this
     */
    public function setOwnerType($ownerType);

    /**
     * Get owner name
     *
     * @return string
     */
    public function getOwnerName();

    /**
     * Set owner name
     *
     * @param string $ownerName
     * @return $this
     */
    public function setOwnerName($ownerName);

    /**
     * Get owner id
     *
     * @return int
     */
    public function getOwnerId();

    /**
     * Set owner id
     *
     * @param int $ownerId
     * @return $this
     */
    public function setOwnerId($ownerId);
}
