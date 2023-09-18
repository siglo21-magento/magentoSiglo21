<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit;

use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Magento\Catalog\Helper\Product\Composite as ProductCompositeHelper;
use Magento\Framework\View\Result\Layout as ResultLayout;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Item\Loader as QuoteItemLoader;

/**
 * Class ConfigureQuoteItems
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit
 */
class ConfigureQuoteItems extends BackendAction
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @var ProductCompositeHelper
     */
    private $productHelper;

    /**
     * @var QuoteItemLoader
     */
    private $quoteItemLoader;

    /**
     * @param Context $context
     * @param DataObjectFactory $dataObjectFactory
     * @param QuoteSession $quoteSession
     * @param ProductCompositeHelper $productHelper
     * @param QuoteItemLoader $quoteItemLoader
     */
    public function __construct(
        Context $context,
        DataObjectFactory $dataObjectFactory,
        QuoteSession $quoteSession,
        ProductCompositeHelper $productHelper,
        QuoteItemLoader $quoteItemLoader
    ) {
        parent::__construct($context);
        $this->dataObjectFactory = $dataObjectFactory;
        $this->quoteSession = $quoteSession;
        $this->productHelper = $productHelper;
        $this->quoteItemLoader = $quoteItemLoader;
    }

    /**
     * @inheritdoc
     *
     * @return ResultLayout
     */
    public function execute()
    {
        $configureResult = $this->dataObjectFactory->create();
        try {
            $quoteItemId = (int) $this->getRequest()->getParam('id');
            if (!$quoteItemId) {
                throw new LocalizedException(__('Quote item ID is required.'));
            }
            $quoteItem = $this->quoteItemLoader->load($quoteItemId);

            $configureResult->setOk(true);
            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setProductId($quoteItem->getProductId());
            $configureResult->setCurrentCustomerId($this->quoteSession->getCustomerId());
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        return $this->productHelper->renderConfigureResult($configureResult);
    }
}
