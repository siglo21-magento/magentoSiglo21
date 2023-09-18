<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\AwCtqQuote\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CreatedBy
 *
 * @package Aheadworks\Ca\ViewModel\AwCtqQuote\Column
 */
class CreatedBy implements ArgumentInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Retrieve customer name from quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @throws LocalizedException
     * @return string
     */
    public function getCreatedBy($quote)
    {
        try {
            $customer = $this->customerRepository->getById($quote->getCustomerId());
            $createdBy = $customer->getFirstname() . ' ' . $customer->getLastname();
        } catch (NoSuchEntityException $exception) {
            $createdBy = '';
        }

        return $createdBy;
    }
}
