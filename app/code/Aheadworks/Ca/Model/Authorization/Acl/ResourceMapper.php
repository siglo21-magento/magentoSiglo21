<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Authorization\Acl;

/**
 * Class ResourceMapper
 * @package Aheadworks\Ca\Model\Authorization\Acl
 */
class ResourceMapper
{
    /**
     * @var array
     */
    private $map = [];

    /**
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $this->prepareMap($map);
    }

    /**
     * Retrieve resource by path
     *
     * @param string $path
     * @return string|null
     */
    public function getResourceByPath($path)
    {
        $path = $this->preparePath($path);
        foreach ($this->map as $resourceId => $resourcePath) {
            if (in_array($path, $resourcePath)) {
                return $resourceId;
            }
        }
        return null;
    }

    /**
     * Prepare path
     *
     * @param string $path
     * @return string
     */
    private function preparePath($path)
    {
        $path = trim(trim($path, '/'));
        $path = strtolower($path);

        return $path;
    }

    /**
     * Prepare map
     *
     * @param array $map
     * @return array
     */
    private function prepareMap($map)
    {
        foreach ($map as &$resourcePath) {
            $resourcePath = array_map('strtolower', $resourcePath);
        }
        return $map;
    }
}
