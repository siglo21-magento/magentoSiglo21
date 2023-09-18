<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Api;

/**
 * Interface GroupManagementInterface
 * @api
 */
interface GroupManagementInterface
{
    /**
     * Create default group for company
     *
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createDefaultGroup();

    /**
     * Save group for company
     *
     * @param \Aheadworks\Ca\Api\Data\GroupInterface $group
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveGroup($group);
}
