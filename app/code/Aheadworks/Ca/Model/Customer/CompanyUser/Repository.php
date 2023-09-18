<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterfaceFactory;
use Aheadworks\Ca\Model\Company;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser as CompanyUserResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class CompanyUserRepository
 * @package Aheadworks\Ca\Model\Customer
 */
class Repository
{
    /**
     * @var CompanyUserResourceModel
     */
    private $resource;

    /**
     * @var CompanyUserInterfaceFactory
     */
    private $companyUserInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param CompanyUserResourceModel $resource
     * @param CompanyUserInterfaceFactory $companyUserInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        CompanyUserResourceModel $resource,
        CompanyUserInterfaceFactory $companyUserInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->companyUserInterfaceFactory = $companyUserInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($customerId)
    {
        if (!isset($this->registry[$customerId])) {
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $this->companyUserInterfaceFactory->create();
            $this->resource->load($companyUser, $customerId);
            if (!$companyUser->getCustomerId()) {
                throw NoSuchEntityException::singleField('customer_id', $customerId);
            }
            $this->registry[$customerId] = $this->getDataObject($companyUser);
        }
        return $this->registry[$customerId];
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotSaveException
     */
    public function save(CompanyUserInterface $companyUser)
    {
        try {
            $companyUser->setHasDataChanges(true);
            $this->resource->save($companyUser);
            $customerId = $companyUser->getCustomerId();
            $this->registry[$customerId] = $companyUser;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $companyUser;
    }

    /**
     * Retrieves data object using model
     *
     * @param CompanyUserInterface $model
     * @return CompanyUserInterface
     */
    private function getDataObject($model)
    {
        /** @var CompanyUserInterface $object */
        $object = $this->companyUserInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, CompanyUserInterface::class),
            CompanyUserInterface::class
        );
        return $object;
    }
}
