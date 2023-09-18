<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface HistoryActionInterface
 * @api
 */
interface HistoryActionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const TYPE = 'type';
    const STATUS = 'status';
    const NAME = 'name';
    const OLD_VALUE = 'old_value';
    const VALUE = 'value';
    const ACTIONS = 'actions';
    /**#@-*/

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     *
     * @param string|null $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get old value
     *
     * @return mixed|null
     */
    public function getOldValue();

    /**
     * Set old value
     *
     * @param mixed|null $oldValue
     * @return $this
     */
    public function setOldValue($oldValue);

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get actions
     *
     * @return \Aheadworks\Ctq\Api\Data\HistoryActionInterface[]|null
     */
    public function getActions();

    /**
     * Set actions
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryActionInterface[]|null $actions
     * @return $this
     */
    public function setActions($actions);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ctq\Api\Data\HistoryActionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryActionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\HistoryActionExtensionInterface $extensionAttributes
    );
}
