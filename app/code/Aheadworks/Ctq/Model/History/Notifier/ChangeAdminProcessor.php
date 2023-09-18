<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\History\Notifier;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\Ctq\Model\History\Notifier\VariableProcessor\Composite as VariableProcessorComposite;
use Aheadworks\Ctq\Model\Magento\ModuleUser\UserRepository;
use Aheadworks\Ctq\Model\Quote\Url;
use Aheadworks\Ctq\Model\Source\History\Action\Type;
use Aheadworks\Ctq\Model\Source\History\EmailVariables;
use Exception;
use Magento\Framework\App\Area;

/**
 * Class Processor
 * @package Aheadworks\Ctq\Model\History\Notifier
 */
class ChangeAdminProcessor
{
    /**
     * @var Config
     */
    protected $config;

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
     * @var Url
     */
    private $url;

    /**
     * @param Config $config
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorComposite $variableProcessorComposite
     * @param UserRepository $userRepository
     * @param Url $url
     */
    public function __construct(
        Config $config,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorComposite $variableProcessorComposite,
        UserRepository $userRepository,
        Url $url
    ) {
        $this->config = $config;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
        $this->userRepository = $userRepository;
        $this->url = $url;
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

        $actions = $history->getActions();
        if (count($actions)
            && end($actions)->getType() == Type::CHANGE_ADMIN
        ) {
            $storeId = $quote->getStoreId();
            /** @var EmailMetadataInterface $emailMetaData */
            $emailMetaData = $this->emailMetadataFactory->create();
            $emailMetaData
                ->setTemplateId($this->getTemplateId($quote))
                ->setTemplateOptions($this->getTemplateOptions($storeId))
                ->setTemplateVariables($this->prepareTemplateVariables($quote))
                ->setSenderName($this->getSenderName($storeId))
                ->setSenderEmail($this->getSenderEmail($storeId))
                ->setRecipientName($this->getRecipientName($quote))
                ->setRecipientEmail($this->getRecipientEmail($quote));
            $emailMetaDataObjects[] = $emailMetaData;
        }
        return $emailMetaDataObjects;
    }

    /**
     * Retrieve seller name
     *
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getRecipientName($quote)
    {
        try {
            $user = $this->userRepository->getById($quote->getSellerId());
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (Exception $e) {
            $name = '';
        }
        return $name;
    }

    /**
     * Retrieve seller email
     *
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getRecipientEmail($quote)
    {
        try {
            $user = $this->userRepository->getById($quote->getSellerId());
            $email = $user->getEmail();
        } catch (Exception $e) {
            $email = '';
        }
        return $email;
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
     * Retrieve template id
     *
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getTemplateId($quote)
    {
        $storeId = $quote->getStoreId();
        return $this->config->getQuoteAdminChangeTemplate($storeId);
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
     * @param QuoteInterface $quote
     * @return array
     */
    protected function prepareTemplateVariables($quote)
    {
        $templateVariables = [
            EmailVariables::QUOTE => $quote,
            EmailVariables::USER_NAME => $this->getRecipientName($quote),
            EmailVariables::QUOTE_URL => $this->url->getAdminQuoteUrl($quote->getId())
        ];
        return $templateVariables;
    }
}
