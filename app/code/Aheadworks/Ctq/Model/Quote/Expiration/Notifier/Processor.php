<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Expiration\Notifier;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\EmailVariables;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\Quote\Expiration\Notifier\VariableProcessor\Composite as VariableProcessorComposite;

/**
 * Class Processor
 *
 * @package Aheadworks\Ctq\Model\Quote\Expiration\Notifier
 */
class Processor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var VariableProcessorComposite
     */
    private $variableProcessorComposite;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorComposite $variableProcessorComposite
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorComposite $variableProcessorComposite
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
    }

    /**
     * Process
     *
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface
     */
    public function process($quote)
    {
        $storeId = $quote->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($quote))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($quote))
            ->setRecipientEmail($this->getRecipientEmail($quote))
            ->setCc($this->getCc($quote));

        return $emailMetaData;
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    private function getTemplateId($storeId)
    {
        return $this->config->getExpirationReminderTemplate($storeId);
    }

    /**
     * Retrieve recipient name
     *
     * @param QuoteInterface $quote
     * @return string
     */
    private function getRecipientName($quote)
    {
        try {
            $customer = $this->customerRepository->getById($quote->getCustomerId());
            $name = $customer->getFirstname() . ' ' . $customer->getLastname();
        } catch (\Exception $e) {
            $name = '';
        }

        return $name;
    }

    /**
     * Retrieve recipient email
     *
     * @param QuoteInterface $quote
     * @return string
     */
    private function getRecipientEmail($quote)
    {
        try {
            $customer = $this->customerRepository->getById($quote->getCustomerId());
            $email = $customer->getEmail();
        } catch (\Exception $e) {
            $email = '';
        }

        return $email;
    }

    /**
     * Retrieve cc
     *
     * @param QuoteInterface $quote
     * @return string|null
     */
    private function getCc($quote)
    {
        return $quote->getCcEmailReceiver();
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderName($storeId)
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderEmail($storeId)
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    private function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param QuoteInterface $quote
     * @return array
     */
    private function prepareTemplateVariables($quote)
    {
        $templateVariables = [
            EmailVariables::QUOTE => $quote,
            EmailVariables::CUSTOMER_NAME => $this->getRecipientName($quote)
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }
}
