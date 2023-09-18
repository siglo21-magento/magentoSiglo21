<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Api;

/**
 * Transaction CRUD interface
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * Save transaction data
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionInterface $transaction
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\CreditLimit\Api\Data\TransactionInterface $transaction);

    /**
     * Retrieve transaction data by ID
     *
     * @param  int $transactionId
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($transactionId);

    /**
     * Retrieve transactions matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionSearchResultsInterface;
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
