<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Model\UrlInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;

/**
 * Class CustomerFormNotification
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company
 */
class CustomerFormNotification
{
    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @param CompanyRepositoryInterface $companyRepository
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        CompanyRepositoryInterface $companyRepository,
        UrlInterface $backendUrl
    ) {
        $this->companyRepository = $companyRepository;
        $this->backendUrl = $backendUrl;
    }

    /**
     * Get notification message
     *
     * @param int $companyId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getHtmlMessage($companyId)
    {
        $company = $this->companyRepository->get($companyId);
        $companyUrl = $this->backendUrl->getUrl(
            "aw_ca/company/edit",
            [
                CompanyInterface::ID => $company->getId()
            ]
        );

        $link = "<a href ='" . $companyUrl . "'>" . __('Company Credit Limit') . "</a>";
        return __('The customer now belongs to a company "%1". ' .
            'Below is Customer\'s independent Credit History, if any. %2' .
            'To view Customer\'s current Сredit History as part of the Company, ' .
            'proceed to the %3.', $company->getName(), '</br>', $link);
    }
}
