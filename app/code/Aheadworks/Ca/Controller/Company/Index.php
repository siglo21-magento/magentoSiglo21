<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Company;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

/**
 * Class Index
 *
 * @package Aheadworks\Ca\Controller\Company
 */
class Index extends AbstractCompanyAction
{
    /**
     * {@inheritdoc}
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Company Information'));

        return $resultPage;
    }
}
