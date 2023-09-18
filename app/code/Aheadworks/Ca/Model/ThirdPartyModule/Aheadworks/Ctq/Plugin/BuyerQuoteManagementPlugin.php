<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Model\CtqManagement;

/**
 * Class BuyerQuoteManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class BuyerQuoteManagementPlugin
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var CtqManagement
     */
    private $ctqManagement;

    /**
     * @param CtqManagement $ctqManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CtqManagement $ctqManagement,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->ctqManagement = $ctqManagement;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Change quote customer to allow buy quote by different customer
     *
     * @param \Aheadworks\Ctq\Api\BuyerQuoteManagementInterface $subject
     * @param bool $result
     * @param int $quoteId
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterBuy($subject, $result, $quoteId)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser && $this->ctqManagement->isAvailableView()) {
            $this->ctqManagement->assignCustomerToQuote($currentUser, $quoteId);
        }

        return $result;
    }

    /**
     * Change quote customer for copied quote
     *
     * @param \Aheadworks\Ctq\Api\BuyerQuoteManagementInterface $subject
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $copiedResult
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterCopyQuote($subject, $copiedResult)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser && $this->ctqManagement->isAvailableView()) {
            $this->ctqManagement->assignCustomerToQuote($currentUser, $copiedResult);
        }

        return $copiedResult;
    }

    /**
     * Change quote customer before updating quote
     *
     * @param \Aheadworks\Ctq\Api\BuyerQuoteManagementInterface $subject
     * @param callable $proceed
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundUpdateQuote($subject, callable $proceed, $quote)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        $isAllowed = $currentUser && $this->ctqManagement->isAvailableView();

        if ($isAllowed) {
            $quote->setCustomerId($currentUser->getId());
        }
        $result = $proceed($quote);
        if ($isAllowed) {
            $this->ctqManagement->assignCustomerToQuote($currentUser, $result);
        }

        return $result;
    }

    /**
     * Change quote customer for quote when changing status
     *
     * @param \Aheadworks\Ctq\Api\BuyerQuoteManagementInterface $subject
     * @param callable $proceed
     * @param int $quoteId
     * @param string $status
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundChangeStatus($subject, callable $proceed, $quoteId, $status)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        $isAllowed = $currentUser && $this->ctqManagement->isAvailableView();

        if ($isAllowed) {
            $quote = $this->ctqManagement->getQuoteRepository()->get($quoteId);
            $quote->setCustomerId($currentUser->getId());
        }
        $result = $proceed($quoteId, $status);
        if ($isAllowed) {
            $this->ctqManagement->assignCustomerToQuote($currentUser, $result);
        }

        return $result;
    }
}
