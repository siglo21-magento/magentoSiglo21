<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\Component\Listing\MassAction\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Framework\UrlInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class Options
 *
 * @package Aheadworks\Ca\Ui\Component\Listing\MassAction\Company
 */
class Options implements \JsonSerializable
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Manager
     */
    private $thirdPartyModuleManager;

    /**
     * @param UrlInterface $urlBuilder
     * @param CompanyRepositoryInterface $companyRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Manager $thirdPartyModuleManager
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CompanyRepositoryInterface $companyRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Manager $thirdPartyModuleManager
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->companyRepository = $companyRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * Get action options
     *
     * @return array
     * @throws LocalizedException
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $companyList = $this->companyRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            $options = [];
            foreach ($companyList as $company) {
                $options[$company->getId()] = [
                    'id' => $company->getId(),
                    'type' => 'company_' . $company->getId(),
                    'label' => $company->getName(),
                    'url' => $this->prepareUrl($company),
                    'confirm' => $this->prepareConfirmAlert()
                ];
            }

            $this->options = array_values($options);
        }

        return $this->options;
    }

    /**
     * Prepare URL
     *
     * @param CompanyInterface $company
     * @return string
     */
    private function prepareUrl($company)
    {
        return $this->urlBuilder->getUrl('aw_ca/company/MassAssignCustomer', ['id' => $company->getId()]);
    }

    /**
     * Prepare confirm alert
     *
     * @return array
     */
    private function prepareConfirmAlert()
    {
        $message =  __('Are you sure to assign selected customers to the company?');
        if ($this->thirdPartyModuleManager->isAwCreditLimitModuleEnabled()) {
            $message .= '</br></br>' .  __('These customers will see only Company Credit Limit on the frontend. ' .
            'Customer\'s credit history can be viewed on the backend.') . '</br>';
        }
        return [
            'title' => __('Assign to the Company'),
            'message' => $message
        ];
    }
}
