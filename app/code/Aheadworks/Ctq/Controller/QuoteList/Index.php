<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\Permission\Checker;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Aheadworks\Ctq\Controller\QuoteList
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Checker $checker
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Checker $checker
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->checker->isAllowed()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Quote List'));

            return $resultPage;
        } else {
            return $this->_redirect('noRoute');
        }
    }
}
