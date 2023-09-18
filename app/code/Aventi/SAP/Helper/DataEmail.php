<?php

namespace Aventi\SAP\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;
use Zend_Mime;
use Zend_Mime_Part;

/**
 * Class Data
 *
 * @package Aventi\SAP\Helper
 */
class DataEmail extends AbstractHelper
{
    const PATH_STORE = 'general/store_information/name';
    const PATH_URL = 'web/secure/base_url';
    const PATH_EMAIL = 'trans_email/ident_general/email';
    const PATH_CUSTOM_EMAIL_1 = 'trans_email/ident_custom1/email';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var Data
     */
    private $data;

    /**
    *  @var File
    */
    private $file;


    /**
     * DataEmail constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param Data $data
     * @param File $file
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        Data $data,
        File $file
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->data = $data;
        $this->file = $file;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getNameStore($store = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::PATH_STORE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getUrlStore($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::PATH_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getEmail($store = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::PATH_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCustomEmail($store = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::PATH_CUSTOM_EMAIL_1,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Send the email  order in coming
     *
     * @param $email
     * @param $name
     * @param $orderId
     * @param $response
     * @param $address
     * @param $city
     * @param $order
     * @param string $sellerEmail
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendOrderEmail(
        $email,
        $name,
        $orderId,
        $response,
        $address,
        $city,
        $order,
        $sellerEmail = "noreply@siglo21.net"
    ) {
        $sender = [
            'name' => $this->getNameStore(),
            'email' =>  $this->getEmail()
        ];
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('order_sent') // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(
                [
                    'email' => $email,
                    'name' => $name,
                    'orderId' => $orderId,
                    'response' => $response,
                    'address' => $address,
                    'city' => $city,
                    'order' => $order
                ]
            )
            ->setFrom($sender)
            ->addTo($email)
            ->addBcc($sellerEmail)
            ->getTransport();
         $transport->sendMessage();
         $this->inlineTranslation->resume();
    }

    /**
     * Send the email cancel order
     *
     * @param $email
     * @param $name
     * @param $orderId
     * @param $payment
     * @param $order
     * @param string $sellerEmail
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendOrderCancelEmail($email, $name, $orderId, $payment, $order, $sellerEmail = "noreply@siglo21.net")
    {

        $sender = [
            'name' => $this->getNameStore(),
            'email' =>  $this->getEmail()
        ];
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('order_cancel')
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(
                [
                    'email' => $email,
                    'name' => $name,
                    'orderId' => $orderId,
                    'payment' => $payment,
                    'order' => $order
                ]
            )
            ->setFrom($sender)
            ->addTo($email)
            ->addBcc($sellerEmail)
            ->getTransport();
         $transport->sendMessage();
         $this->inlineTranslation->resume();
    }

    /**
     * Add an attachment to the message inside the transport builder.
     *
     * @param TransportInterface $transport
     * @param array $file Sanitized index from $_FILES
     * @return TransportInterface
     */
    protected function addAttachment(TransportInterface $transport, array $file = [])
    {
        $part = $this->createAttachment($file);
        $transport->getMessage()->getContent()->addPart($part);

        return $transport;
    }

    /**
     * Create a zend mime part that is an attachment to attach to the email.
     * This was my use case, you'll need to edit this to your own needs.
     *
     * @param array $file Sanitized index from $_FILES
     * @return Zend_Mime_Part
     */
    protected function createAttachment(array $file = [])
    {
        $ext =  '.' . explode('/', $file['type'])[1];
        $fileName = md5(uniqid(microtime()), true) . $ext;

        $attachment = new Zend_Mime_Part($this->file->read($file['tmp_name']));
        $attachment->type = Zend_Mime::MULTIPART_MIXED;
        $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        $attachment->encoding = Zend_Mime::ENCODING_BASE64;
        $attachment->filename = $file['name'];

        return $attachment;
    }
}
