<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface CommentInterface
 * @api
 */
interface CommentInterface extends OwnerInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const QUOTE_ID = 'quote_id';
    const CREATED_AT = 'created_at';
    const COMMENT = 'comment';
    const ATTACHMENTS = 'attachments';
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
     * Get comment
     *
     * @return string
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment);

    /**
     * Get attachments
     *
     * @return \Aheadworks\Ctq\Api\Data\CommentAttachmentInterface[]
     */
    public function getAttachments();

    /**
     * Set attachments
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentAttachmentInterface[] $attachments
     * @return $this
     */
    public function setAttachments($attachments);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ctq\Api\Data\CommentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ctq\Api\Data\CommentExtensionInterface $extensionAttributes
    );
}
