<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Aheadworks\Ctq\Model\Attachment\File\Uploader as FileUploader;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Upload
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Upload extends Action
{
    /**
     * @var string
     */
    const FILE_ID = 'attachments';

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param FileUploader $fileUploader
     * @param Config $config
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        FileUploader $fileUploader,
        Config $config
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->fileUploader = $fileUploader;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
            return parent::dispatch($request);
        }

        return parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $fileId = $this->getRequest()->getParam('param_name') ? : self::FILE_ID;
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $result = $this->fileUploader
                ->setAllowedExtensions($this->config->getAllowFileExtensions())
                ->saveToTmpFolder($fileId);
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }
        return $resultJson->setData($result);
    }
}
