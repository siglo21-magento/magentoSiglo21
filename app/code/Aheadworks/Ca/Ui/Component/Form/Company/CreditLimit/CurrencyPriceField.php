<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\Component\Form\Company\CreditLimit;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\Locale\CurrencyInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class CurrencyPriceField
 *
 * @package Aheadworks\Ca\Ui\Component\Form\Company\CreditLimit
 */
class CurrencyPriceField extends Field
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $thirdPartyModuleManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param ObjectManagerInterface $objectManager
     * @param CurrencyInterface $currency
     * @param Manager $thirdPartyModuleManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CompanyUserManagementInterface $companyUserManagement,
        ObjectManagerInterface $objectManager,
        CurrencyInterface $currency,
        Manager $thirdPartyModuleManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->companyUserManagement = $companyUserManagement;
        $this->objectManager = $objectManager;
        $this->currency = $currency;
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        $companyId = $this->context->getRequestParam(CompanyInterface::ID);
        if ($companyId && $this->thirdPartyModuleManager->isAwCreditLimitModuleEnabled()) {
            try {
                $rootUser = $this->companyUserManagement->getRootUserForCompany($companyId);
                $summary = $this->getSummaryRepository()->getByCustomerId($rootUser->getId());
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

    /**
     * Get summary repository
     *
     * @return \Aheadworks\CreditLimit\Api\SummaryRepositoryInterface
     */
    private function getSummaryRepository()
    {
        return $this->objectManager->get(
            \Aheadworks\CreditLimit\Api\SummaryRepositoryInterface::class
        );
    }
}
