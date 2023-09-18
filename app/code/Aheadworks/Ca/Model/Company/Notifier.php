<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Company\Notifier\EmailProcessor\EmailProcessorInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Sender;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Model\Company\Notifier\ProcessorResolver;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Notifier
 *
 * @package Aheadworks\Ca\Model\Company
 */
class Notifier
{
    /**#@+
     * Constants for processors
     */
    const NEW_COMPANY_CREATED_PROCESSOR = 'new_company_created_processor';
    const NEW_COMPANY_APPROVED_PROCESSOR = 'new_company_approved_processor';
    const NEW_COMPANY_DECLINED_PROCESSOR = 'new_company_declined_processor';
    const COMPANY_STATUS_CHANGED_PROCESSOR = 'company_status_changed_processor';
    /**#@-*/

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var ProcessorResolver
     */
    private $processorResolver;

    /**
     * @var EmailProcessorInterface[]
     */
    private $processors;

    /**
     * @param Sender $sender
     * @param LoggerInterface $logger
     * @param CompanyRepositoryInterface $companyRepository
     * @param ProcessorResolver $processorResolver
     * @param array $processors
     */
    public function __construct(
        Sender $sender,
        LoggerInterface $logger,
        CompanyRepositoryInterface $companyRepository,
        ProcessorResolver $processorResolver,
        $processors = []
    ) {
        $this->sender = $sender;
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
        $this->processorResolver = $processorResolver;
        $this->processors = $processors;
    }

    /**
     * Notify about new created company
     *
     * @param CompanyInterface $company
     * @return void
     * @throws LocalizedException
     */
    public function notifyOnNewCompanyCreated($company)
    {
        $emailMetadataObjects = $this->processors[self::NEW_COMPANY_CREATED_PROCESSOR]->process($company);
        $this->send($emailMetadataObjects);
    }

    /**
     * Notify about company status changes
     *
     * @param CompanyInterface $company
     * @param string|null $oldStatus
     * @return void
     * @throws LocalizedException
     */
    public function notifyOnCompanyStatusChange($company, $oldStatus = null)
    {
        $processorName = $this->processorResolver->resolve($company, $oldStatus);
        if (isset($this->processors[$processorName])) {
            $emailMetadataObjects = $this->processors[$processorName]->process($company);
            $this->send($emailMetadataObjects);
            $this->companyRepository->save($company);
        }
    }

    /**
     * Send email
     *
     * @param EmailMetadataInterface[] $emailMetadataObjects
     * @throws LocalizedException
     */
    private function send($emailMetadataObjects)
    {
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
    }
}
