<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface CommentAttachmentInterface
 * @api
 */
interface CommentAttachmentInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const COMMENT_ID = 'comment_id';
    const NAME = 'name';
    const FILE_NAME = 'file_name';
    /**#@-*/

    /**
     * Get comment id
     *
     * @return int
     */
    public function getCommentId();

    /**
     * Set comment id
     *
     * @param int $commentId
     * @return $this
     */
    public function setCommentId($commentId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);
}
