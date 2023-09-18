<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Model\Company\CompanyManagement;
use Aheadworks\Ca\Model\Source\Company\Status;
use Aheadworks\Ca\Model\Config;
use Exception;

/**
 * Class SellerCompanyService
 * @package Aheadworks\Ca\Model\Service
 */
class SellerCompanyService implements SellerCompanyManagementInterface
{
    /**
     * @var CompanyManagement
     */
    private $companyManagement;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CompanyManagement $companyManagement
     * @param CompanyRepositoryInterface $cartRepository
     * @param Config $config
     */
    public function __construct(
        CompanyManagement $companyManagement,
        CompanyRepositoryInterface $cartRepository,
        Config $config
    ) {
        $this->companyManagement = $companyManagement;
        $this->companyRepository = $cartRepository;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function createCompany($company, $customer)
    {
        if (!$company->getStatus()) {
            $company->setStatus(Status::PENDING_APPROVAL);
        }

        $salesRepresentativeId = $this->config->getDefaultSalesRepresentative($customer->getWebsiteId());
        if ($salesRepresentativeId) {
            $company->setSalesRepresentativeId($salesRepresentativeId);
        }
        return $this->companyManagement->createCompany($company, $customer);
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function updateCompany($company, $customer)
    {
        return $this->companyManagement->updateCompany($company, $customer);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isBlockedCompany($companyId)
    {
        return $this->companyManagement->isBlocked($companyId);
    }

    /**
     * @inheritdoc
     */
    public function changeStatus($companyId, $status)
    {
        return $this->companyManagement->changeStatus($companyId, $status);
    }

    /**
     * @inheritdoc
     */
    public function getCompanyByCustomerId($customerId)
    {
        return $this->companyManagement->getCompanyByCustomerId($customerId);
    }
}
