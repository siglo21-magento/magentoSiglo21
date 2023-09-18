<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterfaceFactory;

/**
 * Class TransactionParametersFactory
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
class TransactionParametersFactory
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TransactionParametersInterfaceFactory
     */
    private $transactionParametersFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param TransactionParametersInterfaceFactory $transactionParametersFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        TransactionParametersInterfaceFactory $transactionParametersFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->transactionParametersFactory = $transactionParametersFactory;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return TransactionParametersInterface
     */
    public function create(array $data = [])
    {
        $transactionParameters = $this->transactionParametersFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $transactionParameters,
            $data,
            TransactionParametersInterface::class
        );
        return $transactionParameters;
    }
}
