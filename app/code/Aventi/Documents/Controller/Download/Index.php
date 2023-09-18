<?php

namespace Aventi\Documents\Controller\Download;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Aventi\Documents\Helper\Data
     */
    private $data;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Aventi\Documents\Helper\Data $data
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Aventi\Documents\Helper\Data $data,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->data = $data;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $sku = $this->getRequest()->getParam('sku');
        $sku = str_replace('.pdf', '', $sku);
        $type = $this->getRequest()->getParam('type', '1');
        if ($this->data->haveDirectory($sku)) {
            try {
                $fullpath = $this->data->getNameFile($sku, $type);
                if (file_exists($fullpath)) {
                    $productEntity =  $this->productRepository->get($sku);
                    header('Content-Disposition: attachment; filename="' . $this->data->createUrlKey($productEntity->getName()) . '.pdf"');
                    if (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') === false) {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/pdf');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($fullpath));
                        readfile($fullpath);
                        exit;
                    } else {
                        readfile($fullpath);
                    }
                }
            } catch (Exception $e) {
                $this->_redirect('/');
            }
        }
    }
}
