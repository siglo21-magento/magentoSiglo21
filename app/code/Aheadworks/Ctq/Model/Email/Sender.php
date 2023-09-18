<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderFactory;

/**
 * Class Sender
 * @package Aheadworks\Ctq\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilderFactory
     */
    private $transportBuilderFactory;

    /**
     * @param TransportBuilderFactory $transportBuilderFactory
     */
    public function __construct(
        TransportBuilderFactory $transportBuilderFactory
    ) {
        $this->transportBuilderFactory = $transportBuilderFactory;
    }

    /**
     * Send email message
     *
     * @param EmailMetadataInterface $emailMetadata
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send($emailMetadata)
    {
        /** @var TransportBuilder $transportBuilder */
        $transportBuilder = $this->transportBuilderFactory->create();

        $transportBuilder
            ->setTemplateIdentifier($emailMetadata->getTemplateId())
            ->setTemplateOptions($emailMetadata->getTemplateOptions())
            ->setTemplateVars($emailMetadata->getTemplateVariables())
            ->setFrom(['name' => $emailMetadata->getSenderName(), 'email' => $emailMetadata->getSenderEmail()])
            ->addTo($emailMetadata->getRecipientEmail(), $emailMetadata->getRecipientName());

        if ($emailMetadata->getCc()) {
            $transportBuilder->addCc($emailMetadata->getCc());
        }
        if ($emailMetadata->getBcc()) {
            $transportBuilder->addBcc($emailMetadata->getBcc());
        }

        $transportBuilder
            ->getTransport()
            ->sendMessage();
    }
}
