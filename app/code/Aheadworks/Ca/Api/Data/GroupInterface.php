<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface GroupInterface
 * @api
 */
interface GroupInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const PARENT_ID = 'parent_id';
    const PATH = 'path';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get parent id
     *
     * @return int
     */
    public function getParentId();

    /**
     * Set parent id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get created at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\GroupExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\GroupExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\GroupExtensionInterface $extensionAttributes
    );
}
