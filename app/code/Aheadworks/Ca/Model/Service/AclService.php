<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\AclManagementInterface;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\Acl\RootResource;
use Magento\Framework\Acl\AclResource\ProviderInterface;

/**
 * Class AclService
 * @package Aheadworks\Ca\Model\Service
 */
class AclService implements AclManagementInterface
{
    /**
     * @var RootResource
     */
    private $rootResource;

    /**
     * @var ProviderInterface
     */
    private $aclResourceProvider;

    /**
     * @param RootResource $rootResource
     * @param ProviderInterface $aclResourceProvider
     */
    public function __construct(
        RootResource $rootResource,
        ProviderInterface $aclResourceProvider
    ) {
        $this->rootResource = $rootResource;
        $this->aclResourceProvider = $aclResourceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootResourceId()
    {
        return $this->rootResource->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceKeys()
    {
        $resources = $this->getResourceStructure();

        return $this->mapResources($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceStructure()
    {
        return $this->aclResourceProvider->getAclResources();
    }

    /**
     * Map resources
     *
     * @param array $resources
     * @return array
     */
    private function mapResources($resources)
    {
        $output = [];
        foreach ($resources as $resource) {
            $output[] = $resource['id'];
            $output = array_merge($output, $this->mapResources($resource['children']));
        }
        return $output;
    }
}
