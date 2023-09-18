<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Administrator
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier
 */
class Administrator implements ModifierInterface
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $result = $data;
        if (!empty($data) && !isset($data['company'])) {
            $newData['company'] = $data;
            if (isset($data[CompanyInterface::ID]) && !empty($data[CompanyInterface::ID])) {
                $newData = array_merge(
                    $newData,
                    $this->getCustomerInformation($data[CompanyInterface::ID])
                );
            }
            $result = $newData;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get customer information as array
     *
     * @param int $companyId
     * @return array
     */
    private function getCustomerInformation($companyId)
    {
        try {
            $rootCompanyUser = $this->companyUserManagement->getRootUserForCompany($companyId);
            $rootCompanyUserData = $this->dataObjectProcessor->buildOutputDataArray(
                $rootCompanyUser,
                CustomerInterface::class
            );
        } catch (NoSuchEntityException $exception) {
            $rootCompanyUserData = [];
        }

        return $rootCompanyUserData;
    }
}
