<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface HistoryInterface
 * @api
 */
interface HistoryInterface extends OwnerInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const QUOTE_ID = 'quote_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const ACTIONS = 'actions';
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
     * Get quote id
     *
     * @return int
     */
    public function getQuoteId();

    /**
     * Set quote id
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId);

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
     * Get actions
     *
     * @return \Aheadworks\Ctq\Api\Data\HistoryActionInterface[]
     */
    public function getActions();

    /**
     * Set actions
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryActionInterface[] $actions
     * @return $this
     */
    public function setActions($actions);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ctq\Api\Data\HistoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\HistoryExtensionInterface $extensionAttributes
    );
}
