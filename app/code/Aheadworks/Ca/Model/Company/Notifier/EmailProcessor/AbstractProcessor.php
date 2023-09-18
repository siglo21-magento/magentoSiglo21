<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Email\EmailMetadataInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AbstractProcessor
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
abstract class AbstractProcessor implements EmailProcessorInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CompanyUserManagementInterface
     */
    protected $companyUserManagement;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    protected $emailMetadataFactory;

    /**
     * @var VariableProcessorInterface
     */
    protected $variableProcessorComposite;

    /**
     * @var CustomerInterface
     */
    protected $rootCustomer;

    /**
     * @param Config $config
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorInterface $variableProcessorComposite
     */
    public function __construct(
        Config $config,
        CompanyUserManagementInterface $companyUserManagement,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorInterface $variableProcessorComposite
    ) {
        $this->config = $config;
        $this->companyUserManagement = $companyUserManagement;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
    }

    /**
     * @inheritdoc
     */
    public function process($company)
    {
        $storeId = $this->getRootCustomer($company)->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($company, $storeId))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($company))
            ->setRecipientEmail($this->getRecipientEmail($company));

        return [$emailMetaData];
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    protected function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param CompanyInterface $company
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    protected function prepareTemplateVariables($company, $storeId)
    {
        $templateVariables = [
            EmailVariables::COMPANY => $company,
            EmailVariables::STORE => $this->storeManager->getStore($storeId),
            EmailVariables::CUSTOMER => $this->getRootCustomer($company)
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    protected function getSenderName($storeId)
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    protected function getSenderEmail($storeId)
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Retrieve root customer
     *
     * @param CompanyInterface $company
     * @return CustomerInterface
     */
    protected function getRootCustomer($company)
    {
        /*if ($this->rootCustomer === null) {
            $this->rootCustomer = $this->companyUserManagement->getRootUserForCompany($company->getId());
        }*/
        $this->rootCustomer = $this->companyUserManagement->getRootUserForCompany($company->getId());
        return $this->rootCustomer;
    }

    /**
     * Retrieve recipient name
     *
     * @param CompanyInterface $company
     * @return string
     */
    abstract protected function getRecipientName($company);

    /**
     * Retrieve recipient email
     *
     * @param CompanyInterface $company
     * @return string
     */
    abstract protected function getRecipientEmail($company);

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    abstract protected function getTemplateId($storeId);
}
