<?php

namespace Solicitud\CustomerTab\Controller\Front;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_customerSession;
    protected $_url;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_url = $url;
        parent::__construct($context);
    }

    public function execute()
    {
        // Verifica si el cliente está autenticado
        if ($this->_customerSession->isLoggedIn()) {
            // Obtiene el ID del cliente
            $customerId = $this->_customerSession->getCustomer()->getId();

            // Crea una página
            $resultPage = $this->resultPageFactory->create();

            // Configura la vista para utilizar la plantilla tab_content.phtml
            $resultPage->getConfig()->getTitle()->set(__('Solicitud de Crédito'));
            
            // Pasa el ID del cliente a la vista
            $block = $resultPage->getLayout()->getBlock('tab_content');
            if ($block) {
                $block->setData('customer_id', $customerId);
            }
            
            return $resultPage;
        } else {
            // Si el cliente no está autenticado, redirigirlo a la página de inicio de sesión
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getUrl('customer/account/login'));
            return $resultRedirect;
        }
    }
}