<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AsyncJobInterface
 *
 * @package Aheadworks\CreditLimit\Api\Data
 */
interface JobInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TYPE = 'type';
    const CONFIGURATION = 'configuration';
    const STATUS = 'status';
    /**#@-*/

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set configuration
     *
     * @param string $configuration
     * @return $this
     */
    public function setConfiguration($configuration);

    /**
     * Get configuration
     *
     * @return string
     */
    public function getConfiguration();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\CreditLimit\Api\Data\JobExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\JobExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\JobExtensionInterface $extensionAttributes
    );
}
