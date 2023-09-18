<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Forbidden;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Index
 *
 * @package Aheadworks\Ca\Controller\Forbidden
 */
class Index extends Action
{
    const FORBIDDEN_PAGE_ID = 'aw-ca-forbidden-page';

    /**
     * @var PageHelper
     */
    private $helper;

    /**
     * @param Context $context
     * @param PageHelper $pageHelper
     */
    public function __construct(
        Context $context,
        PageHelper $pageHelper
    ) {
        parent::__construct($context);
        $this->helper = $pageHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultPage = $this->helper->prepareResultPage($this, self::FORBIDDEN_PAGE_ID);
        if ($resultPage) {
            $resultPage->setStatusHeader(403, '1.1', 'Forbidden');
            return $resultPage;
        }

        throw new NotFoundException(__('Page not found.'));
    }
}
