<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Authorization\Acl\ResourceMapper;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\AuthorizationInterface;
use Aheadworks\Ca\Model\Authorization\CustomProcessor\ProcessorInterface;

/**
 * Class AuthorizationService
 * @package Aheadworks\Ca\Model\Service
 */
class AuthorizationService implements AuthorizationManagementInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ResourceMapper
     */
    private $resourceMapper;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var ProcessorInterface
     */
    private $customProcessor;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param AuthorizationInterface $authorization
     * @param ResourceMapper $resourceMapper
     * @param UserContextInterface $userContext
     * @param ProcessorInterface $customProcessor
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        AuthorizationInterface $authorization,
        ResourceMapper $resourceMapper,
        UserContextInterface $userContext,
        ProcessorInterface $customProcessor,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->authorization = $authorization;
        $this->resourceMapper = $resourceMapper;
        $this->userContext = $userContext;
        $this->customProcessor = $customProcessor;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($path)
    {
        $resource = $this->resourceMapper->getResourceByPath($path);
        return $resource ? $this->isAllowedByResource($resource) : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByResource($resource)
    {
        $result = true;
        if ($this->userContext->getUserId()) {
            $currentUser = $this->companyUserManagement->getCurrentUser();
            $module = explode('::', $resource)[0];
            if ($currentUser) {
                $isAllowedResource = $this->authorization->isAllowed($resource);
                $customProcessorResult = $this->customProcessor->isAllowed($resource);

                $result = $isAllowedResource && $customProcessorResult;
            } elseif (!$currentUser && $module == 'Aheadworks_Ca') {
                $result = false;
            }
        }

        return $result;
    }
}
