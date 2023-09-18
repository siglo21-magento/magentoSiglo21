<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aventi\PriceList\Plugin\Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aventi\PriceList\Model\Session;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class Edit
 * @package Aventi\PriceList\Plugin\Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Edit
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Session
     */
    private $session;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Session $session
     * @param QuoteRepositoryInterface $quoteRepository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $session,
        QuoteRepositoryInterface $quoteRepository,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->quoteRepository = $quoteRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Index action
     *
     * @return ResultPage|Redirect
     * @throws LocalizedException
     */
    public function beforeExecute()
    {

        $quoteId = $this->request->getParam('id', null);
        if ($quoteId) {
            try {
                $quote = $this->quoteRepository->get($quoteId);
                $this->session->unsCustomerAdminhtml();
                $this->session->setCustomerAdminhtml($quote->getCustomerId());
            } catch (NoSuchEntityException $exception) {
                $this->logger->debug("ERROR TO GET QUOTE ID IN PRICELIST: " . $exception->getMessage());
            }
        }
    }
}
