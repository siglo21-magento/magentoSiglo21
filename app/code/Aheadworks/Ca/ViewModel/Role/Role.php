<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\ViewModel\Role;

use Aheadworks\Ca\Api\AclManagementInterface;
use Aheadworks\Ca\Model\Url;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Role
 * @package Aheadworks\Ca\ViewModel\Role
 */
class Role implements ArgumentInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var AclManagementInterface
     */
    private $aclManagement;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param Url $url
     * @param AclManagementInterface $aclManagement
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Url $url,
        AclManagementInterface $aclManagement,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->url = $url;
        $this->aclManagement = $aclManagement;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Round amount
     *
     * @param float $amount
     * @return float
     */
    public function getRoundAmount($amount)
    {
        return $this->priceCurrency->round($amount);
    }

    /**
     * Retrieve edit url
     *
     * @param int $customerId
     * @return string
     */
    public function getEditUrl($customerId)
    {
        return $this->url->getFrontendEditRoleUrl($customerId);
    }

    /**
     * Retrieve delete url
     *
     * @param int $customerId
     * @return string
     */
    public function getDeleteUrl($customerId)
    {
        return $this->url->getFrontendDeleteRoleUrl($customerId);
    }

    /**
     * Retrieve role tree
     *
     * @return array
     */
    public function getRoleTree()
    {
        $aclResources = $this->aclManagement->getResourceStructure();

        return $this->mapResources($aclResources);
    }

    /**
     * Make ACL resource array compatible with jQuery jsTree component
     *
     * @param array $resources
     * @return array
     */
    public function mapResources(array $resources)
    {
        $output = [];
        foreach ($resources as $resource) {
            $item = [];
            $item['id'] = $resource['id'];
            $item['text'] = __($resource['title']);
            $item['children'] = [];
            if (isset($resource['children'])) {
                $item['state'] = [
                    'opened' => 'open'
                ];
                $item['children'] = $this->mapResources($resource['children']);
            }
            $output[] = $item;
        }
        return $output;
    }
}
