<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Block\Adminhtml\Quote;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Header as EditPageHeader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Block\Widget\Context as WidgetContext;
use Aheadworks\Ctq\Api\SellerActionManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ctq\Api\Data\QuoteActionInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote
 */
class Edit extends FormContainer
{
    /**
     * @var SellerActionManagementInterface
     */
    private $sellerActionManagement;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param WidgetContext $context
     * @param SellerActionManagementInterface $sellerActionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param array $data
     */
    public function __construct(
        WidgetContext $context,
        SellerActionManagementInterface $sellerActionManagement,
        QuoteRepositoryInterface $quoteRepository,
        array $data = []
    ) {
        $this->sellerActionManagement = $sellerActionManagement;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return void
     * @throws NoSuchEntityException
     */
    protected function _construct()
    {
        $this->setId('aw_ctq_quote_edit');

        $quoteId = $this->_request->getParam('id', null);
        $quoteActions = $this->getQuoteActions($quoteId);
        foreach ($quoteActions as $key => $quoteAction) {
            $this->addButton(
                strtolower($quoteAction->getName()),
                [
                    'label' => __($quoteAction->getName()),
                    'onclick' => 'quote.submit({"url": "' . $this->getUrl($quoteAction->getUrlPath()) . '"})',
                    'class' => $key == 0 ? 'primary' : '',
                    'data_attribute' => []
                ],
                -1
            );
        }

        if (!$quoteId) {
            $this->addSaveButton();
        }

        $this->addBackButton();
    }

    /**
     * @inheritdoc
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        $pageTitle = $this->getLayout()->createBlock(EditPageHeader::class)->toHtml();
        if (is_object($this->getLayout()->getBlock('page.title'))) {
            $this->getLayout()->getBlock('page.title')->setPageTitle($pageTitle);
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare header HTML
     *
     * @return string
     * @throws LocalizedException
     */
    public function getHeaderHtml()
    {
        $headerBlockOutput = $this->getLayout()->createBlock(EditPageHeader::class)->toHtml();
        return '<div id="quote-header">' . $headerBlockOutput . '</div>';
    }

    /**
     * Get header width
     *
     * @return string
     */
    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('aw_ctq/quote/index');
    }

    /**
     * Get list of actions available for current quote
     *
     * @param int $quoteId
     * @return QuoteActionInterface[]
     * @throws NoSuchEntityException
     */
    private function getQuoteActions($quoteId)
    {
        $actions = [];
        if ($quoteId) {
            $quote = $this->quoteRepository->get($quoteId);
            $actions = $this->sellerActionManagement->getAvailableQuoteActions($quote);
        }

        return $actions;
    }

    /**
     * Add save button on form
     */
    private function addSaveButton()
    {
        $this->addButton(
            'save',
            [
                'label' => __('Save'),
                'onclick' => 'quote.submit({})',
                'style' => 'display:none',
                'class' => 'primary',
                'id' => 'save_quote_button',
                'data_attribute' => []
            ],
            -1
        );
    }

    /**
     * Add back button on form
     */
    private function addBackButton()
    {
        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('aw_ctq/quote/index') . '\')',
                'class' => 'back'
            ],
            -1
        );
    }
}
