<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser\Notifier;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\EmailVariables;
use Aheadworks\Ca\Model\Email\EmailMetadataInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class NewCompanyUserCreated
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser\Notifier
 */
class NewCompanyUserCreated implements EmailProcessorInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CompanyRepositoryInterface
     */
    protected $companyRepository;

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
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorInterface $variableProcessorComposite
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorInterface $variableProcessorComposite,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function process($customer)
    {
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($customer->getStoreId()))
            ->setTemplateOptions($this->getTemplateOptions($customer->getStoreId()))
            ->setTemplateVariables($this->prepareTemplateVariables($customer))
            ->setSenderName($this->getSenderName($customer->getStoreId()))
            ->setSenderEmail($this->getSenderEmail($customer->getStoreId()))
            ->setRecipientName($this->getRecipientName($customer))
            ->setRecipientEmail($this->getRecipientEmail($customer));

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
     * @param CustomerInterface $customer
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function prepareTemplateVariables($customer)
    {
        $companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser();
        $company = $this->companyRepository->get($companyUser->getCompanyId());
        $templateVariables = [
            EmailVariables::COMPANY => $company,
            EmailVariables::CUSTOMER => $customer
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
     * Retrieve recipient name
     *
     * @param CustomerInterface $customer
     * @return string
     */
    protected function getRecipientName($customer)
    {
        return $customer->getFirstname() . ' ' .  $customer->getLastname();
    }

    /**
     * Retrieve recipient email
     *
     * @param CustomerInterface $customer
     * @return string
     */
    protected function getRecipientEmail($customer)
    {
        return $customer->getEmail();
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getNewCompanyUserCreatedTemplate($storeId);
    }
}
