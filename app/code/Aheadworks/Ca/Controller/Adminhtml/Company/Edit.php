<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Controller\Adminhtml\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

/**
 * Class Edit
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\Company
 */
class Edit extends Action
{
    /**
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CompanyRepositoryInterface $companyRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CompanyRepositoryInterface $companyRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->companyRepository = $companyRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return ResultPage|ResultRedirect
     */
    public function execute()
    {
        $companyId = (int)$this->getRequest()->getParam('id');
        if ($companyId) {
            try {
                $company = $this->companyRepository->get($companyId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This company does not exist.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Ca::companies')
            ->getConfig()->getTitle()->prepend(
                $companyId
                    ? __('Edit "%1" company', $company->getName())
                    : __('New Company')
            );
        return $resultPage;
    }
}
