<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface QuoteActionInterface
 * @api
 */
interface QuoteActionInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const TYPE = 'type';
    const NAME = 'name';
    const URL_PATH = 'url_path';
    const SORT_ORDER = 'sort_order';
    /**#@-*/

    /**
     * Retrieve type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve url path
     *
     * @return string
     */
    public function getUrlPath();

    /**
     * Retrieve sort order
     *
     * @return int
     */
    public function getSortOrder();
}
