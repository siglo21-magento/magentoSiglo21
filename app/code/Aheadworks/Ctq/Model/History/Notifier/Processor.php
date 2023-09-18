<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\History\Notifier;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\Ctq\Model\Source\History\Action\Type;
use Aheadworks\Ctq\Model\Source\History\EmailVariables;
use Aheadworks\Ctq\Model\Source\History\Status;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\History\Notifier\VariableProcessor\Composite as VariableProcessorComposite;
use Aheadworks\Ctq\Model\Magento\ModuleUser\UserRepository;
use Aheadworks\Ctq\Model\Source\Quote\Status as QuoteStatus;

/**
 * Class Processor
 * @package Aheadworks\Ctq\Model\History\Notifier
 */
class Processor
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    protected $emailMetadataFactory;

    /**
     * @var VariableProcessorComposite
     */
    protected $variableProcessorComposite;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorComposite $variableProcessorComposite
     * @param UserRepository $userRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorComposite $variableProcessorComposite,
        UserRepository $userRepository
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
        $this->userRepository = $userRepository;
    }

    /**
     * Process
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface[]
     */
    public function process($history, $quote)
    {
        $emailMetaDataObjects = [];
        if ($this->isNewQuote($history)) {
            $emailMetaDataObjects = $this->processForBoth($history, $quote);
        } else {
            $emailMetaDataObjects[] = $this->processForSingle($history, $quote);
        }

        return $emailMetaDataObjects;
    }

    /**
     * Process for both
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface[]
     */
    protected function processForBoth($history, $quote)
    {
        $recipients = [Owner::SELLER => $quote->getSellerId(), Owner::BUYER => $quote->getCustomerId()];
        $emailMetaDataObjects = [];
        $historyMod = clone $history;
        foreach ($recipients as $ownerType => $recipientId) {
            $historyMod
                ->setOwnerType($ownerType)
                ->setOwnerId($recipientId);

            $emailMetaDataObjects[] = $this->processForSingle($historyMod, $quote);
        }

        return $emailMetaDataObjects;
    }

    /**
     * Process for single
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface
     */
    protected function processForSingle($history, $quote)
    {
        $storeId = $quote->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($history, $quote))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($history, $quote))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($history, $quote))
            ->setRecipientEmail($this->getRecipientEmail($history, $quote))
            ->setCc($this->getCc($history, $quote));

        // set bcc to seller if quote expired
        if (count($history->getActions()) == 1) {
            /** @var HistoryActionInterface $historyAction */
            $historyAction = current($history->getActions());
            if ($historyAction->getType() == Type::QUOTE_ATTRIBUTE_STATUS
                && $historyAction->getValue() == QuoteStatus::EXPIRED
            ) {
                $emailMetaData->setBcc($this->getBcc($quote));
            }
        }

        return $emailMetaData;
    }

    /**
     * Check if new quote
     *
     * @param HistoryInterface $history
     * @return bool
     */
    protected function isNewQuote($history)
    {
        return $history->getStatus() == Status::CREATED_QUOTE;
    }

    /**
     * Retrieve template id
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getTemplateId($history, $quote)
    {
        $storeId = $quote->getStoreId();
        $isNewQuote = $this->isNewQuote($history);
        if ($this->isNotifierForSeller($history)) {
            $template = $isNewQuote
                ? $this->config->getSellerNewQuoteTemplate($storeId)
                : $this->config->getSellerQuoteChangesTemplate($storeId);
        } else {
            $template = $isNewQuote
                ? $this->config->getBuyerNewQuoteTemplate($storeId)
                : $this->config->getBuyerQuoteChangesTemplate($storeId);
        }

        return $template;
    }

    /**
     * Retrieve recipient name
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getRecipientName($history, $quote)
    {
        if ($this->isNotifierForSeller($history)) {
            $name = $this->getSellerName($quote);
        } else {
            $name = $this->getBuyerName($quote);
        }

        return $name;
    }

    /**
     * Retrieve buyer name
     *
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getBuyerName($quote)
    {
        try {
            $customer = $this->customerRepository->getById($quote->getCustomerId());
            $name = $customer->getFirstname() . ' ' .  $customer->getLastname();
        } catch (\Exception $e) {
            $name = '';
        }
        return $name;
    }

    /**
     * Retrieve seller name
     *
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getSellerName($quote)
    {
        try {
            $user = $this->userRepository->getById($quote->getSellerId());
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = '';
        }
        return $name;
    }

    /**
     * Retrieve recipient email
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getRecipientEmail($history, $quote)
    {
        try {
            if ($this->isNotifierForSeller($history)) {
                $user = $this->userRepository->getById($quote->getSellerId());
                $email = $user->getEmail();
            } else {
                $customer = $this->customerRepository->getById($quote->getCustomerId());
                $email = $customer->getEmail();
            }
        } catch (\Exception $e) {
            $email = '';
        }
        return $email;
    }

    /**
     * Retrieve cc
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string|null
     */
    protected function getCc($history, $quote)
    {
        $email = null;
        if ($this->isNotifierForSeller($history)) {
            $storeId = $quote->getStoreId();
            $email = $this->config->getRecipientsEmail($storeId);
        } else {
            $email = $quote->getCcEmailReceiver();
        }
        return $email;
    }

    /**
     * Retrieve bcc
     *
     * @param QuoteInterface $quote
     * @return array
     */
    protected function getBcc($quote)
    {
        $emails = [];

        /** @noinspection PhpUnhandledExceptionInspection */
        try {
            $user = $this->userRepository->getById($quote->getSellerId());
            $emails[] = $user->getEmail();
        } catch (\Exception $e) {
        }

        $storeId = $quote->getStoreId();
        $emails = array_merge($emails, $this->config->getRecipientsEmail($storeId));

        return $emails;
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
     * Check if notifier for seller
     *
     * @param HistoryInterface $history
     * @return bool
     */
    protected function isNotifierForSeller($history)
    {
        return $history->getOwnerType() == Owner::BUYER;
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
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return array
     */
    protected function prepareTemplateVariables($history, $quote)
    {
        $templateVariables = [
            EmailVariables::HISTORY => $history,
            EmailVariables::QUOTE => $quote,
            EmailVariables::STORE => $this->storeManager->getStore($quote->getStoreId()),
            EmailVariables::USER_NAME => $this->getRecipientName($history, $quote),
            EmailVariables::IS_SELLER => $this->isNotifierForSeller($history),
            EmailVariables::BUYER_NAME => $this->getBuyerName($quote)
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }
}
