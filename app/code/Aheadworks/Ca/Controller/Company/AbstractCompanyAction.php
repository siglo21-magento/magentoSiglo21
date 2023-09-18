<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Controller\AbstractCustomerAction;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class AbstractCompanyAction
 *
 * @package Aheadworks\Ca\Controller\Company
 */
abstract class AbstractCompanyAction extends AbstractCustomerAction
{
    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CompanyRepositoryInterface $companyRepository
    ) {
        parent::__construct($context, $customerSession);
        $this->companyRepository = $companyRepository;
    }

    /**
     * Retrieve company
     *
     * @return CompanyInterface
     * @throws NotFoundException
     */
    protected function getEntity()
    {
        try {
            $entity = $this->companyRepository->get($this->getCurrentCompanyId());
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }
        return $entity;
    }

    /**
     * @inheritdoc
     */
    protected function isEntityBelongsToCustomer()
    {
        if (!$this->isForwardAction(['create'])) {
            $entity = $this->getEntity();

            if (!$entity->getId()
                && $this->getCurrentCompanyId() != $entity->getId()
            ) {
                return false;
            }
        }

        return true;
    }
}
