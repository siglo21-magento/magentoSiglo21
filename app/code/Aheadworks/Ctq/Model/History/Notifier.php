<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\History;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Aheadworks\Ctq\Model\Email\Sender;
use Aheadworks\Ctq\Model\Email\Template\RenderState;
use Aheadworks\Ctq\Model\History\Notifier\ChangeAdminProcessor;
use Aheadworks\Ctq\Model\History\Notifier\Processor;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface;

/**
 * Class Notifier
 * @package Aheadworks\Ctq\Model\History
 */
class Notifier
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var Processor
     */
    private $baseEmailProcessor;

    /**
     * @var ChangeAdminProcessor
     */
    private $changeAdminEmailProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RenderState
     */
    private $renderState;

    /**
     * @param Sender $sender
     * @param Processor $emailProcessor
     * @param ChangeAdminProcessor $changeAdminProcessor
     * @param LoggerInterface $logger
     * @param RenderState $renderState
     */
    public function __construct(
        Sender $sender,
        Processor $emailProcessor,
        ChangeAdminProcessor $changeAdminProcessor,
        LoggerInterface $logger,
        RenderState $renderState
    ) {
        $this->sender = $sender;
        $this->baseEmailProcessor = $emailProcessor;
        $this->changeAdminEmailProcessor = $changeAdminProcessor;
        $this->logger = $logger;
        $this->renderState = $renderState;
    }

    /**
     * Notify of new history actions
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function notify($history, $quote)
    {
        $emailMetadataObjects = $this->baseEmailProcessor->process($history, $quote);
        $emailMetadataObjects = array_merge(
            $emailMetadataObjects,
            $this->changeAdminEmailProcessor->process($history, $quote)
        );
        $this->renderState->isRendering(true);

        /** @var EmailMetadataInterface $emailMetadata */
        foreach ($emailMetadataObjects as $emailMetadata) {
            try {
                if ($emailMetadata->getRecipientEmail()) {
                    $this->sender->send($emailMetadata);
                }
            } catch (MailException $e) {
                $this->logger->critical($e);
            }
        }
        $this->renderState->isRendering(false);
    }
}
