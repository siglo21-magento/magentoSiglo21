<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Ui\Component\Form\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Field;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\Locale\CurrencyInterface;

/**
 * Class CurrencyPriceField
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Form\Customer
 */
class CurrencyPriceField extends Field
{
    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SummaryRepositoryInterface $summaryRepository
     * @param CurrencyInterface $currency
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SummaryRepositoryInterface $summaryRepository,
        CurrencyInterface $currency,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->summaryRepository = $summaryRepository;
        $this->currency = $currency;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        $customerId = $this->context->getRequestParam('id');
        if ($customerId) {
            try {
                $summary = $this->summaryRepository->getByCustomerId($customerId);
                $currency = $this->currency->getCurrency($summary->getCurrency());
                $beforeText = $currency->getSymbol();
            } catch (NoSuchEntityException $noSuchEntityException) {
                $beforeText = '';
            }
            $config = $this->getData('config');
            $config['addbefore'] = $beforeText;
            $this->setData('config', $config);
        }
    }
}
