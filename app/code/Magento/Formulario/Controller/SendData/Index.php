<?php
namespace Magento\Formulario\Controller\SendData;


use Magento\Framework\Filesystem\Io\File;
use Fpdf\Fpdf;

class Index extends \Magento\Framework\App\Action\Action
{

    const PATH_STORE = 'general/store_information/name';
    const PATH_URL = 'web/secure/base_url';
    const PATH_EMAIL = 'trans_email/ident_general/email';

    protected $resultPageFactory;

    /**
    *   @var \Aventi\SAP\Helper\DataEmail
    *
    */
    protected $helperMail;

    protected $jsonHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    private $_file;

    protected $_transportBuilder;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Aventi\SAP\Helper\DataEmail $helperMail,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_file = $file;
        $this->jsonHelper = $jsonHelper;
        $this->helperMail = $helperMail;
        parent::__construct($context);
        $this->logger = $logger;
    }


    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // $pdf = new FPDF();
        // $pdf->AddPage();
        // $pdf->SetFont('Arial','B',16);
        // $pdf->Cell(40,10,'¡Hola, Mundo!');
        // $pdf->Output();

    	$data = '';
        $data = $this->getRequest()->getParams();
        $files = $this->getRequest()->getFiles();


        $sender = [
            'name' => 'Siglo 21',
            'email' =>  'info@siglo21.net'
        ];
        
        $receiver = "mdillon@siglo21.net";
        $transport = $this->_transportBuilder->setTemplateIdentifier('outsourcing_form') // put Email Template Name
              ->setTemplateOptions(['area' => 'frontend', 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
              ->setTemplateVars($data)
              ->setFrom($sender)
              ->addTo($receiver)
              ->addBcc('jgomez@siglo21.net');

        foreach ($files as $file) {
            $this->_transportBuilder->addAttachment($this->_file->read($file['tmp_name']), $file['name'], $file['type']);
        }

        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        return $this->jsonResponse($sender);
        
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }



}
