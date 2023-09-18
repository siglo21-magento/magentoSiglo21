<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Acl\AclResource;

use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\Acl\AclResource\ProviderInterface;
use Magento\Framework\Acl\AclResource\TreeBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Acl\Data\CacheInterface;
use Aheadworks\Ca\Model\Authorization\CustomProcessor\ProcessorInterface;

/**
 * Class Provider
 * @package Aheadworks\Ca\Model\Authorization\Acl\AclResource
 */
class Provider implements ProviderInterface
{
    /**
     * @var ReaderInterface
     */
    private $configReader;

    /**
     * @var TreeBuilder
     */
    private $resourceTreeBuilder;

    /**
     * @var CacheInterface
     */
    private $aclDataCache;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var Manager
     */
    private $thirdPartyManager;

    /**
     * @var ProcessorInterface
     */
    private $customProcessor;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @param ReaderInterface $configReader
     * @param TreeBuilder $resourceTreeBuilder
     * @param CacheInterface $aclDataCache
     * @param Json $serializer
     * @param Manager $thirdPartyManager
     * @param ProcessorInterface $customProcessor
     * @param string $cacheKey
     */
    public function __construct(
        ReaderInterface $configReader,
        TreeBuilder $resourceTreeBuilder,
        CacheInterface $aclDataCache,
        Json $serializer,
        Manager $thirdPartyManager,
        ProcessorInterface $customProcessor,
        $cacheKey = null
    ) {
        $this->configReader = $configReader;
        $this->resourceTreeBuilder = $resourceTreeBuilder;
        $this->aclDataCache = $aclDataCache;
        $this->serializer = $serializer;
        $this->thirdPartyManager = $thirdPartyManager;
        $this->customProcessor = $customProcessor;
        $this->cacheKey = $cacheKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclResources()
    {
        $tree = $this->aclDataCache->load($this->cacheKey);
        if ($tree) {
            $tree = $this->serializer->unserialize($tree);
            return $this->prepareTree($tree);
        }
        $aclResourceConfig = $this->configReader->read();
        if (!empty($aclResourceConfig['config']['acl']['resources'])) {
            $tree = $this->resourceTreeBuilder->build($aclResourceConfig['config']['acl']['resources']);
            $this->aclDataCache->save($this->serializer->serialize($tree), $this->cacheKey);
            $tree = $this->prepareTree($tree);
            return $tree;
        }
        return [];
    }

    /**
     * Prepare tree
     *
     * @param array $tree
     * @return array
     */
    private function prepareTree($tree)
    {
        $preparedTree = [];
        foreach ($tree as $resource) {
            $resourceId = $resource['id'];
            $resourceParams = explode('::', $resourceId);
            $moduleName = $resourceParams[0];
            if (in_array($moduleName, $this->thirdPartyManager->getAll())
                && !$this->thirdPartyManager->isModuleEnabledByName($moduleName)
                || !$this->customProcessor->isAllowed($resourceId)
            ) {
                continue;
            }

            if (isset($resource['children'])) {
                $resource['children'] = $this->prepareTree($resource['children']);
            }
            $preparedTree[] = $resource;
        }
        return $preparedTree;
    }
}
