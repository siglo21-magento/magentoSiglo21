<?php

namespace Aventi\SAP\Plugin\Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aventi\SAP\Api\Data\QuoteStatusInterface;
use Aventi\SAP\Api\DocumentStatusRepositoryInterface;
use Aventi\SAP\Model\Sync\SendToSAP;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SendToSAP
     */
    private $sendToSap;
    /**
     * @var DocumentStatusRepositoryInterface
     */
    private $documentStatusRepository;

    public function __construct(
        LoggerInterface $logger,
        DocumentStatusRepositoryInterface $documentStatusRepository
    ) {
        $this->logger = $logger;
        $this->documentStatusRepository = $documentStatusRepository;
    }

    public function afterExecute(\Aheadworks\Ctq\Controller\Adminhtml\Quote\Save $subject, $result)
    {
        $response = $subject->getRequest()->getPostValue();
        if (isset($response['quote'])) {
            if (isset($response['quote']['quote_id'])) {
                try {
                    $documentStatus = $this->documentStatusRepository->getByParentId($response['quote']['quote_id']);
                    $documentStatus->setSapStatus(QuoteStatusInterface::QUOTE_SYNC);
                    $this->documentStatusRepository->save($documentStatus);
                } catch (LocalizedException $e) {
                    $this->logger->debug("Error to get the documentstatus by parent: " . $e->getMessage());
                }
            }
        }

        return $result;
    }
}
