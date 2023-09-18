<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Backend\Model\Url as BackendUrl;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Url as FrontendUrl;
use Magento\Framework\UrlInterface;

/**
 * Class Url
 * @package Aheadworks\Ca\Model
 */
class Url
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var FrontendUrl
     */
    private $urlBuilderFrontend;

    /**
     * @var BackendUrl
     */
    private $urlBuilderBackend;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @param UrlInterface $urlBuilder
     * @param FrontendUrl $urlBuilderFrontend
     * @param BackendUrl $urlBuilderBackend
     * @param CustomerRegistry $customerRegistry
     */
    public function __construct(
        UrlInterface $urlBuilder,
        FrontendUrl $urlBuilderFrontend,
        BackendUrl $urlBuilderBackend,
        CustomerRegistry $customerRegistry
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->urlBuilderFrontend = $urlBuilderFrontend;
        $this->urlBuilderBackend = $urlBuilderBackend;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * Retrieve create company url
     * @return string
     */
    public function getFrontendCreateCompanyFormUrl()
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/company/create');
    }

    /**
     * Retrieve edit customer url
     * @param $customerId
     * @return string
     */
    public function getFrontendEditCustomerUrl($customerId)
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/user/edit', ['id' => $customerId]);
    }

    /**
     * Retrieve customer change status url
     *
     * @param int $customerId
     * @param bool $needActivate
     * @return string
     */
    public function getFrontendCustomerChangeStatusUrl($customerId, $needActivate)
    {
        return $this->urlBuilderFrontend->getUrl(
            'aw_ca/user/changeStatus',
            [
                'id' => $customerId,
                'activate' => (int)$needActivate
            ]
        );
    }

    /**
     * Retrieve edit role url
     * @param $roleId
     * @return string
     */
    public function getFrontendEditRoleUrl($roleId)
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/role/edit', ['id' => $roleId]);
    }

    /**
     * Retrieve delete role url
     * @param $roleId
     * @return string
     */
    public function getFrontendDeleteRoleUrl($roleId)
    {
        return $this->urlBuilderFrontend->getUrl('aw_ca/role/delete', ['id' => $roleId]);
    }

    /**
     * Retrieve reset password url
     * @param CustomerInterface|Customer $customer
     * @return string
     */
    public function getResetPasswordUrl($customer)
    {
        $customerSecureData = $this->customerRegistry->retrieveSecureData($customer->getId());
        return $this->urlBuilderFrontend->getUrl(
            'customer/account/createPassword',
            ['_query' => ['id' => $customer->getId(), 'token' => $customerSecureData->getRpToken()]]
        );
    }

    /**
     * Get url to company URL in admin
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function getCompanyUrl($company)
    {
        return $this->urlBuilderBackend->getUrl('aw_ca/company/edit', ['id' => $company->getId()]);
    }
}
