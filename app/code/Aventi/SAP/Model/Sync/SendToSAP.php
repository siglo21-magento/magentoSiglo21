<?php

namespace Aventi\SAP\Model\Sync;

use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;
use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\QuoteProcessor;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Aheadworks\Ctq\Model\ResourceModel\Quote\CollectionFactory;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Aventi\PickUpWithOffices\Api\OfficeRepositoryInterface;
use Aventi\SAP\Api\Data\DocumentStatusInterfaceFactory;
use Aventi\SAP\Api\Data\ItemStatusInterfaceFactory;
use Aventi\SAP\Api\Data\QuoteStatusInterface;
use Aventi\SAP\Api\DocumentStatusRepositoryInterface;
use Aventi\SAP\Api\ItemStatusRepositoryInterface;
use Aventi\SAP\Helper\Attribute;
use Aventi\SAP\Helper\Configuration;
use Aventi\SAP\Helper\Data;
use Aventi\SAP\Helper\DataCustomer;
use Aventi\SAP\Helper\DataEmail;
use Aventi\SAP\Helper\SAP;
use Aventi\SAP\Logger\Logger;
use Aventi\SAP\Model\Integration;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;
use Symfony\Component\Console\Helper\ProgressBar;

class SendToSAP extends Integration
{
    const ORDER_TYPE = 17;
    const QUOTE_TYPE = 23;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var \Aventi\SAP\Helper\Data
     */
    private $data;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    private $dataToSAP;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $sap;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Aventi\PickUpWithOffices\Api\OfficeRepositoryInterface
     */
    private $officeRepository;

    /**
     * @var  \Aventi\SAP\Helper\DataEmail
     */
    private $dataEmail;

    /**
     * @var \Magento\Sales\Model\OrderFactory
    */
    protected $_orderFactory;

    /**
    * @var \Magento\Sales\Api\OrderManagementInterface
    */
    private $orderManagement;

    /**
    * @var \Aventi\SAP\Helper\DataCustomer
    */
    private $_dataCustomer;

    /**
     * @var CollectionFactory
     */
    private $_quoteCollectionFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Aventi\SAP\Api\Data\DocumentStatusInterfaceFactory
     */
    private $documentStatusInterfaceFactory;

    /**
     * @var DocumentStatusRepositoryInterface
     */
    private $documentStatusRepository;

    /**
     * @var ItemStatusRepositoryInterface
     */
    private $itemStatusRepositoryInterface;

    /**
     * @var ItemStatusInterfaceFactory
     */
    private $itemStatusInterfaceFactory;

    /**
     * @var \Aventi\SAP\Helper\Configuration
     */
    private $configHelper;

    /**
     * @param Attribute $attribute
     * @param Logger $logger
     * @param DriverInterface $driver
     * @param Filesystem $filesystem
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param Data $data
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param SAP $sap
     * @param DateTime $dateTime
     * @param ResourceConnection $resourceConnection
     * @param ProductRepositoryInterface $productRepository
     * @param OfficeRepositoryInterface $officeRepository
     * @param DataEmail $dataEmail
     * @param OrderFactory $orderFactory
     * @param OrderManagementInterface $orderManagement
     * @param DataCustomer $dataCustomer
     * @param CollectionFactory $quoteCollectionFactory
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CartRepositoryInterface $cartRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param DocumentStatusInterfaceFactory $documentStatusInterfaceFactory
     * @param DocumentStatusRepositoryInterface $documentStatusRepository
     * @param ItemStatusRepositoryInterface $itemStatusRepositoryInterface
     * @param ItemStatusInterfaceFactory $itemStatusInterfaceFactory
     * @param Configuration $configuration
     */
    public function __construct(
        Attribute $attribute,
        Logger $logger,
        DriverInterface $driver,
        Filesystem $filesystem,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Aventi\SAP\Helper\Data $data,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Aventi\SAP\Helper\SAP $sap,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Aventi\PickUpWithOffices\Api\OfficeRepositoryInterface $officeRepository,
        \Aventi\SAP\Helper\DataEmail $dataEmail,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Aventi\SAP\Helper\DataCustomer $dataCustomer,
        CollectionFactory $quoteCollectionFactory,
        QuoteRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        AddressRepositoryInterface $addressRepository,
        \Aventi\SAP\Api\Data\DocumentStatusInterfaceFactory $documentStatusInterfaceFactory,
        DocumentStatusRepositoryInterface $documentStatusRepository,
        ItemStatusRepositoryInterface $itemStatusRepositoryInterface,
        ItemStatusInterfaceFactory $itemStatusInterfaceFactory,
        \Aventi\SAP\Helper\Configuration $configuration
    ) {
        parent::__construct($attribute, $logger, $driver, $filesystem);
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->data = $data;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->sap = $sap;
        $this->dateTime = $dateTime;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->officeRepository = $officeRepository;
        $this->dataEmail = $dataEmail;
        $this->_orderFactory = $orderFactory;
        $this->orderManagement = $orderManagement;
        $this->_dataCustomer = $dataCustomer;
        $this->_quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->cartRepository = $cartRepository;
        $this->addressRepository = $addressRepository;
        $this->documentStatusInterfaceFactory = $documentStatusInterfaceFactory;
        $this->documentStatusRepository = $documentStatusRepository;
        $this->itemStatusRepositoryInterface = $itemStatusRepositoryInterface;
        $this->itemStatusInterfaceFactory = $itemStatusInterfaceFactory;
        $this->configHelper = $configuration;
    }

    /**
     * @param int $operation
     * @param null $quoteEdit
     * @return array[]
     */
    public function quoteToSAP($operation = QuoteStatusInterface::QUOTE_PROCESSING, $quoteEdit = null)
    {
        return $this->processQuote($operation, [Status::PENDING_SELLER_REVIEW, Status::PENDING_BUYER_REVIEW], $quoteEdit);
    }

    /**
     * Send the order to SAP
     *
     * @return array
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 29/11/18
     */
    public function completedOrderToSAP()
    {
        return $this->processOrder(['syncing', 'pending', 'paid_tucompra']);
    }

    /**
     * @return array
     * @author Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 22/08/2019
     */
    public function errorOrderToSAP()
    {
        return $this->processOrder(['error_creacion']);
    }

    /**
     * Delete the registers of interations with SAP and return the number of interations
     *
     * @param string $status
     * @param int $orderId
     * @return int
     */
    public function getNumberInteration($status = 'pending', $orderId = 123456)
    {
        $iterations = 0;
        $historiesModel = $this->historyCollectionFactory->create();
        $historiesModel->addFieldToFilter('entity_name', 'order');
        $historiesModel->addFieldToFilter('status', $status);
        $historiesModel->addFieldToFilter('parent_id', $orderId);
        $historiesModel->addFieldToFilter('comment', ['like' => '%Sincronizando%']);
        $historiesModel->load();

        foreach ($historiesModel as $history) {
            $iterations = intval(preg_replace('/[^0-9]+/i', '', $history->getData('comment')));
            if ($iterations != 10) {
                $history->delete();
            }
        }
        $historiesModel->save();
        return ++$iterations;
    }

    /**
     * Validate if the error description
     *
     * @param string $description
     * @param int $orderId
     * @return int
     */
    public function validateError($description = 'pending', $orderId = 123456)
    {
        $iterations = 0;
        $historiesModel = $this->historyCollectionFactory->create();
        $historiesModel->addFieldToFilter('parent_id', $orderId);
        $historiesModel->addFieldToFilter('comment', ['like' => '%' . $description . '%']);
        $historiesModel->load();
        return count($historiesModel->getItems());
    }

    /**
     * @param $str string of sap
     * @return bool|mixed
     */
    public function validateNumberOrder($str)
    {
        $re = '/("DocNum": ?\d{1,10})/m';
        $numberOrder = false;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (is_array($matches) && !empty($matches)) {
            $numberOrder = str_replace(['"DocNum":', ' '], '', $matches[0][0]);
        }

        return ($numberOrder == false) ? $str : $numberOrder;
    }

    /**
     * @param $str string of sap
     * @return bool|mixed
     */
    public function validateDocEntry($str)
    {
        $re = '/("DocEntry": ?\d{1,10})/m';
        $numberOrder = false;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (is_array($matches) && !empty($matches)) {
            $numberOrder = str_replace(['"DocEntry":', ' '], '', $matches[0][0]);
        }

        return ($numberOrder == false) ? $str : $numberOrder;
    }

    /**
     * @param $str string of sap
     * @return bool|mixed
     */
    public function validateDocType($str)
    {
        $re = '/("DocType": ?\d{1,10})/m';
        $numberOrder = false;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (is_array($matches) && !empty($matches)) {
            $numberOrder = str_replace(['"DocType":', ' '], '', $matches[0][0]);
        }

        return ($numberOrder == false) ? $str : $numberOrder;
    }

    /**
     * Get error description
     *
     * @param $str
     * @return mixed
     */
    public function getErrorDescription($str)
    {
        try {
            $description = str_replace(['"err":"', '"', "{", "}"], '', $str);
            return $description;
        } catch (\Exception $e) {
            return $str;
        }
    }

    /**
     * @param array $status
     * @return array[]
     */
    public function processOrder(array $status = []): array
    {
        $orders = $this->orderCollectionFactory->create()
            ->addAttributeToFilter('status', $status)
            ->addAttributeToFilter('sap_id', ['null' => true])
            ->setOrder('created_at', 'ASC');
        $totalOrders = count($orders);
        $totalOrderSentSAP = $totalOrderError = 0;

        foreach ($orders as $orderData) {
            try {
                $order = $this->orderRepository->get($orderData['entity_id']);
                $iteration = $this->getNumberInteration(['syncing', 'error_creacion'], $order->getId());
                if ($iteration == 10) {
                    $order->addStatusToHistory('syncing', sprintf('Sincronizando pedido con SAP Server (%s intento)', $iteration));
                    $this->orderRepository->save($order);
                    $order->addStatusToHistory('syncing', 'Número de intentos máximos superados');
                    $this->orderRepository->save($order);
                    continue;
                } elseif ($iteration > 10) {
                    continue;
                }
                $products = $this->formatProductsToSAP($order->getAllItems(), $order, self::ORDER_TYPE);
                $order->addStatusToHistory('syncing', sprintf('Sincronizando pedido con SAP Server (%s intento)', $iteration));
                $this->orderRepository->save($order);
                $response = $this->formatAndSendDocumentToSAP(self::ORDER_TYPE, $order, $products);

                switch ($response['status']) {
                    case 201:
                        $numberOrder = $this->validateNumberOrder($response['body']);
                        $docEntry = $this->validateDocEntry($response['body']);
                        $docType = $this->validateDocType($response['body']);
                        $stringStatus = ($docType == 112) ? 'Borrador' : 'Pedido';
                        $orderStatus = ($docType == 112) ? 'pending' : 'processing';
                        $orderState = ($docType == 112) ? 'new' : 'processing';
                        $order->setState($orderState);
                        $order->addStatusToHistory(
                            $orderStatus,
                            sprintf('El ' . $stringStatus . ' <strong>%s</strong> fué ingresado en SAP <strong>%s</strong>', $order->getIncrementId(), $numberOrder)
                        )->save();
                        $order->setData('sap_id', $numberOrder);
                        $order->setData('sap_doc_entry', $docEntry);
                        $order->setData('sap_type', $docType);
                        $this->updateSaleOrderGrid($order->getIncrementId(), $numberOrder);
                        $this->updateSaleOrderGridType($order->getIncrementId(), $docType);
                        $totalOrderSentSAP++;
                        break;
                    case 100:
                        $numberOrder = $this->validateNumberOrder($response['body']);
                        $docType = $this->validateDocType($response['body']);
                        if (is_numeric($numberOrder)) {
                            $order->setData('sap_id', $numberOrder);
                            $this->updateSaleOrderGrid($order->getIncrementId(), $numberOrder);
                            $this->updateSaleOrderGridType($order->getIncrementId(), $docType);
                            $order->addStatusToHistory(
                                'processing',
                                sprintf('El pedido <strong>%s</strong> fué ingresado en SAP <strong>%s</strong>', $order->getIncrementId(), $numberOrder)
                            )->save();
                            $totalOrderSentSAP++;
                            break;
                        } else {
                            $errorDescription = sprintf('<strong>Error de creación</strong><br>%s', $this->getErrorDescription($response['body']));
                            if ($this->validateError($errorDescription, $order->getId()) <= 0) {
                                $order->addStatusToHistory('error_creacion', $errorDescription)->save();
                            }

                            $totalOrderError++;
                            $this->logger->error(json_encode($this->dataToSAP));
                        }
                        // no break
                    default:

                        $errorDescription = sprintf('<strong>Error de creación</strong><br>%s', $this->getErrorDescription($response['body']));
                        if ($this->validateError($errorDescription, $order->getId()) <= 0) {
                            $order->addStatusToHistory('error_creacion', $errorDescription)->save();
                        }
                        $totalOrderError++;
                        $this->logger->error(json_encode($this->dataToSAP));
                        break;

                }
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        return [
            'title' => [_('Total orders'), _('Total error'), _('Total completed')],
            'body' => [
                number_format($totalOrders, 0, ',', '.'),
                number_format($totalOrderError, 0, ',', '.'),
                number_format($totalOrderSentSAP, 0, ',', '.'),
            ]
        ];
    }

    /**
     * Set the sap order id in the sale order grid
     *
     * @param $incrementId
     * @param $orderSapId
     */
    public function updateSaleOrderGrid($incrementId, $orderSapId)
    {
        $table = $this->resourceConnection->getConnection()->getTableName('sales_order_grid');
        $sql = 'UPDATE  __TABLE__ SET sap_id = "__ORDER__" WHERE increment_id  = "__ID__"';
        $search = ['__TABLE__', '__ID__', '__ORDER__'];
        $replace = [$table, $incrementId, $orderSapId];
        $sql = str_replace($search, $replace, $sql);
        $this->resourceConnection->getConnection()->query($sql);
    }

    /**
     * Set the sap type in the sale order grid
     *
     * @param $incrementId
     * @param $orderSapId
     */
    public function updateSaleOrderGridType($incrementId, $docType)
    {
        $table = $this->resourceConnection->getConnection()->getTableName('sales_order_grid');
        $sql = 'UPDATE  __TABLE__ SET sap_type = "__TYPE__" WHERE increment_id  = "__ID__"';
        $search = ['__TABLE__', '__ID__', '__TYPE__'];
        $replace = [$table, $incrementId, $docType];
        $sql = str_replace($search, $replace, $sql);
        $this->resourceConnection->getConnection()->query($sql);
    }

    /**
     * @return void
     * @throws MailException
     */
    public function orderSent()
    {
        $start = 0;
        $rows = 1000;

        $jsonData = $this->data->getResource('order_state', $start, $rows, false);
        $jsonPath = $this->getJsonPath($jsonData, 'order_state');
        if ($jsonPath) {
            $data = json_decode($jsonPath, true);
            if ($data['total'] > 0) {
                foreach ($data['data'] as   $order) {
                    $sapID = $order['DocNum'];
                    $status = $order['Estado'];
                    $orderCollection =  $this->orderCollectionFactory->create()
                        ->addFieldToFilter('sap_id', $sapID)
                        ->addFieldToFilter('sap_notification_send', 0)
                        ->addFieldToFilter('sap_type', 17);
                    foreach ($orderCollection as $orden) {
                        $shippingMethod = $orden->getShippingMethod();

                        if ($status == 'Entregado') {
                            $addressBilling = '';
                            $cityShipping = '';
                            $state = 'Listo para despachar';
                            $stateStatus = 'complete';
                            $response = 'ha sido despachado';

                            if ($shippingMethod == 'pickup_pickup') {
                                $state = 'Listo para recoger';
                                $response = 'está listo para recoger en el almacén ';
                                $stateStatus = 'pick_it_up';

                                if ($orden->getData('pick_up_id') > 0) {
                                    try {
                                        $office = $this->officeRepository->getById($orden->getData('pick_up_id'));
                                        $addressBilling = $office->getAddress();
                                        $cityShipping = $office->getCity();
                                        $response .= $office->getTitle();
                                    } catch (\Exception $e) {
                                        $addressBilling =  $orden->getBillingAddress()->getStreet()[0];
                                        $cityShipping = $orden->getShippingAddress()->getCity();
                                    }
                                } else {
                                    $addressBilling =  $orden->getBillingAddress()->getStreet()[0];
                                    $cityShipping = $orden->getShippingAddress()->getCity();
                                }
                            } else {
                                $addressBilling =  $orden->getBillingAddress()->getStreet()[0];
                                $cityShipping = $orden->getShippingAddress()->getCity();
                            }

                            $order= $this->orderRepository->get($orden->getId());
                            $order->setData('sap_notification_send', 1);
                            $order->setState('complete');
                            $order->setStatus($stateStatus);
                            $order->addStatusHistoryComment($state);
                            $this->orderRepository->save($order);

                            $this->dataEmail->sendOrderEmail(
                                $orden->getCustomerEmail(),
                                $orden->getCustomerFirstname() . ' ' . $orden->getCustomerLastname(),
                                $orden->getIncrementId(),
                                $response,
                                $addressBilling,
                                $cityShipping,
                                $orden,
                                $this->_dataCustomer->getSellerEmail($order->getCustomerId())
                            );
                        }

                    }
                }
            } else {
                echo 'No exiten ordenes para sincronizar ' . PHP_EOL;
            }
        }
    }

    /**
     * @return void
     */
    public function draftStatus()
    {
        $start = 0;
        $rows = 1000;

        $jsonData = $this->data->getResource('draft_order', $start, $rows, false);
        $jsonPath = $this->getJsonPath($jsonData, 'draft_order');
        if ($jsonPath) {
            $data = json_decode($jsonPath, true);
            if ($data['total'] > 0) {
                foreach ($data['data'] as   $order) {
                    $sapID = $order['DocNum'];
                    $docEntry = $order['DocEntry'];
                    $status = $order['WddStatus'];
                    $newSapID = $order['DocNum1'];
                    $newDocEntry = $order['DocEntry1'];
                    $orderCollection =  $this->orderCollectionFactory->create()
                        ->addAttributeToFilter('status', ['pending'])
                        ->addFieldToFilter('sap_id', $sapID)
                        ->addFieldToFilter('sap_doc_entry', $docEntry)
                        ->addFieldToFilter('sap_type', 112);
                    foreach ($orderCollection as $orden) {
                        if ($status == 'N' || $status == '-') {
                            $stateComment = '';
                            $order= $this->orderRepository->get($orden->getId());
                            if ($status == 'N') {
                                $stateComment = 'Draft no autorizado: <b>' . $sapID . '</b>';
                                $this->orderManagement->cancel($orden->getId());
                            } else {
                                if (!is_null($newSapID) && !is_null($newDocEntry)) {
                                    $stateComment = 'Draft: <b>' . $sapID . '</b> pasado a Pedido: <b>' . $newSapID . '<b>';
                                    $order->setStatus('processing');
                                    $order->setData('sap_id', $newSapID);
                                    $order->setData('sap_doc_entry', $newDocEntry);
                                    $order->setData('sap_type', 17);
                                    $this->updateSaleOrderGrid($order->getIncrementId(), $newSapID);
                                    $this->updateSaleOrderGridType($order->getIncrementId(), 17);
                                }
                            }
                            $order->addStatusToHistory($order->getStatus(), $stateComment);
                            $this->orderRepository->save($order);
                        }
                    }
                }
            }
        }
    }

    public function testSendMail($option = 0)
    {
        $order = $this->_orderFactory->create()->loadByIncrementId("000000076");

        switch ($option) {
            case 0:
                $this->dataEmail->sendOrderCancelEmail(
                    'emerz@aventi.com.co',
                    'Hans Merz',
                    "000000076",
                    'Credit Limit',
                    $order
                );
                break;
            case 1:
                $this->dataEmail->sendOrderEmail(
                    $order->getCustomerEmail(),
                    $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                    $order->getIncrementId(),
                    'está listo para recoger en el almacén',
                    'direccion',
                    'QUITO',
                    $order
                );
                // no break
            default:

                break;
        }
    }

    public function getTax($percent)
    {
        $taxClass = 'IVA_EXE';
        if ($percent > 0 && $percent > 12) {
            $taxClass = sprintf('IVA_%s', intval($percent));
        } elseif ($percent == 12) {
            $taxClass = 'IVA';
        }

        return $taxClass;
    }

    public function validateListMaterial($products)
    {
        $isListMaterial = false;
        if (is_array($products)) {
            foreach ($products as $product) {
                if ($product['EsLM'] == 1) {
                    $isListMaterial = true;
                }
            }
        }

        return $isListMaterial;
    }

    /**
     * @param $operation
     * @param $status
     * @param $quoteEdit
     * @return array[]
     */
    public function processQuote($operation = QuoteStatusInterface::QUOTE_PROCESSING, $status, $quoteEdit = null)
    {
        $sapState = $operation;

        $orders = $this->_quoteCollectionFactory->create()
            ->addFieldToFilter('status', $status)
            ->addFieldToFilter(['t1.sap_status', 't1.sap_status'], [['null' => true], ['in' => [QuoteStatusInterface::QUOTE_SYNC]]])
            ->setOrder('created_at', 'ASC');
        $orders->getSelect()->joinLeft(['t1' => 'aventi_sap_documentstatus'], 't1.parent_id = main_table.id');
        $totalOrders = count($orders);
        $totalOrderSentSAP = $totalOrderError = 0;
        foreach ($orders->getData() as $orderData) {
            try {
                $quote = $this->quoteRepository->get($orderData['id']);
                $cart = $this->cartRepository->get($quote->getCartId());
                $products = $this->getQuoteProducts($cart);
                $option = (is_null($orderData['sap_status'])) ? QuoteStatusInterface::QUOTE_CREATED : $orderData['sap_status'];
                $response = $this->formatAndSendDocumentToSAP(self::QUOTE_TYPE, $cart, $products, $quote, $option);

                $docNum = null;
                $docEntry = null;
                $sapResult = null;
                switch ($response['status']) {
                    case 201:
                        $numberOrder = $this->validateNumberOrder($response['body']);
                        $docEntry = $this->validateDocEntry($response['body']);

                        $sapResult = sprintf('La cotización %s fué ingresada en SAP: %s', $quote->getId(), $numberOrder);
                        $docNum = $numberOrder;
                        $quote->setData('sap', $numberOrder);
                        $quote->setData('sap_doc_entry', $docEntry);
                        $quote->setData('sap_state', $sapState);
                        $quote->setData('sap_result', $sapResult);
                        $details = json_decode($response['body'], true);
                        $this->updateItemFromQuote($cart, $details);
                        $totalOrderSentSAP++;
                        break;
                    case 100:
                        $numberOrder = $this->validateNumberOrder($response['body']);
                        $docEntry = $this->validateDocEntry($response['body']);
                        if (is_numeric($numberOrder)) {
                            $sapResult = sprintf('La cotización %s fué ingresada en SAP: %s', $quote->getId(), $numberOrder);
                            $docNum = $numberOrder;
                            $quote->setData('sap', $numberOrder);
                            $quote->setData('sap_doc_entry', $docEntry);
                            $quote->setData('sap_state', $sapState);
                            $quote->setData('sap_result', $sapResult);
                            $details = json_decode($response['body'], true);
                            $this->updateItemFromQuote($cart, $details);
                            $totalOrderSentSAP++;
                            break;
                        } else {
                            $errorDescription = sprintf('Error de creación: %s', $this->getErrorDescription($response['body']));
                            if ($this->validateError($errorDescription, $quote->getId()) <= 0) {
                                $quote->setData('sap_result', $errorDescription);
                            }
                            $sapState = QuoteStatusInterface::QUOTE_SYNC;
                            $totalOrderError++;
                            $this->logger->error(json_encode($this->dataToSAP));
                        }
                    // no break
                    default:
                        $sapState = QuoteStatusInterface::QUOTE_SYNC;
                        $errorDescription = sprintf('Error de creación: %s', $this->getErrorDescription($response['body']));
                        if ($this->validateError($errorDescription, $quote->getId()) <= 0) {
                            $quote->setData('sap_result', $errorDescription);
                        }
                        $totalOrderError++;
                        $this->logger->error(json_encode($this->dataToSAP));
                        break;

                }
                try {
                    if ($documentStatus = $this->documentStatusRepository->getByParentId($quote->getId())) {
                        $documentStatus->setType('quote');
                        $documentStatus->setParentId($quote->getId());
                        $documentStatus->setSap($docNum);
                        $documentStatus->setSapDocEntry($docEntry);
                        $documentStatus->setSapResult($sapResult);
                        $documentStatus->setSapStatus($sapState);
                        $documentStatus->setParentId($quote->getId());
                        $this->documentStatusRepository->save($documentStatus);
                    }
                } catch (LocalizedException $e) {
                    $documentStatus = $this->documentStatusInterfaceFactory->create();
                    $documentStatus->setType('quote');
                    $documentStatus->setParentId($quote->getId());
                    $documentStatus->setSap($docNum);
                    $documentStatus->setSapDocEntry($docEntry);
                    $documentStatus->setSapResult($sapResult);
                    $documentStatus->setSapStatus($sapState);
                    $documentStatus->setParentId($quote->getId());
                    $this->documentStatusRepository->save($documentStatus);
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return [
            'title' => [_('Total orders'), _('Total error'), _('Total completed')],
            'body' => [
                number_format($totalOrders, 0, ',', '.'),
                number_format($totalOrderError, 0, ',', '.'),
                number_format($totalOrderSentSAP, 0, ',', '.')
            ]
        ];
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteProducts($cart)
    {
        if ($cart) {
            return  $this->formatProductsToSAP($cart->getAllItems(), $cart, self::QUOTE_TYPE);
        }
    }

    /**
     * @param $items
     * @param \Magento\Quote\Api\Data\CartInterface | \Magento\Sales\Api\Data\OrderInterface $entity
     * @param int $type
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function formatProductsToSAP($items, $entity, $type = self::ORDER_TYPE)
    {
        $products = [];
        foreach ($items as $item) {
            $product = $this->productRepository->getById($item->getProductId());
            $totalDiscount = 0;
            switch ($type) {
                case self::ORDER_TYPE:
                    if ($entity->getBaseDiscountAmount() < 0) {
                        $totalDiscount = (int) round($entity->getBaseDiscountAmount() * 100 / $entity->getSubtotal()) * -1;
                    } else {
                        $totalDiscount = 0;
                    }
                    if ($totalDiscount == 0) {
                        $totalDiscount = $this->resolveOrderDiscount($item);
                    }
                    break;
                case self::QUOTE_TYPE:
                default:
                    $quote = $this->getQuoteByCartId($entity->getId());
                    $totalDiscount = $this->resolveQuoteDiscount($quote);
                    break;
            }
            $stateSlow = $product->getCustomAttribute('state_slow')->getValue();
            $stateSlow = ($stateSlow == 1) ? 'S' : 'N';
            $brand = $product->getData('u_marca');
            $listMaterial = ($product->getCustomAttribute('list_material')) ? $product->getCustomAttribute('list_material')->getValue() : 0;
            $warehouseLm = '';
            if ($listMaterial) {
                if ($product->getCustomAttribute('bodega_lm')) {
                    $warehouseLm = $product->getCustomAttribute('bodega_lm')->getValue();
                }
                if ($type == self::QUOTE_TYPE) {
                    continue;
                }
            }
            $userFields = [
                "U_Exx_Des_EstadoLentoDet~$stateSlow"
            ];
            $tax = $this->getTax((int)$item->getTaxPercent());
            $userFields = trim(implode("|", $userFields));
            $baseLine = (is_null($item->getLineSap()) || $type == self::QUOTE_TYPE) ? 0 : $item->getLineSap();
            $baseEntry = (is_null($item->getBaseEntry()) || $type == self::QUOTE_TYPE) ? 0 : $item->getBaseEntry();
            $baseType = (is_null($item->getBaseType()) || $type == self::QUOTE_TYPE) ? 0 : $item->getBaseType();
            $products[] = [
                'ItemCode' => $item->getSku(),
                'Quantity' => intval(($type == self::ORDER_TYPE) ? $item->getQtyOrdered() : $item->getTotalQty()),
                'EsLM' => $listMaterial,
                'WhsCode' => $warehouseLm,
                'Price' => $item->getPrice(),
                'BaseLine' => $baseLine,
                'BaseEntry' => $baseEntry,
                'BaseType'=> $baseType,
                'DiscountPercent' => $totalDiscount,
                "TaxCode" => $tax,
                "CamposUsuario" =>  $userFields,
                "Marca" => $brand
            ];
        }

        return $products;
    }

    public function resolveSerieToQuote($serie)
    {
        switch ($serie) {
            case 13:
                $serieName = 'OVG_01';
                break;
            case 104:
                $serieName = 'OVP_01';
                break;
            case 103:
            default:
                $serieName = 'OVQ_01';
                break;
        }
        $serieName = substr_replace($serieName, "C", 0);
        return $this->resolveSerieId($serieName);
    }

    public function resolveSerieId($serieName)
    {
        switch ($serieName) {
            case 'CVQ_01':
                $quoteSerie = 111;
                break;
            case 'CVP_01':
                $quoteSerie = 112;
                break;
            case 'CVG_01':
            default:
                $quoteSerie = 20;
                break;
        }
        return $quoteSerie;
    }

    /**
     * @param int $type
     * @param \Magento\Quote\Api\Data\CartInterface | \Magento\Sales\Api\Data\OrderInterface $entity
     * @param $products
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface | null $quote
     * @param string $operation
     * @return array
     */
    public function formatAndSendDocumentToSAP($type = self::ORDER_TYPE, $entity, $products, $quote = null, $operation = QuoteStatusInterface::QUOTE_CREATED)
    {
        try {
            $idMagento = ($type == self::ORDER_TYPE) ? $entity->getIncrementId() : $quote->getId();
            $attributes = [];
            $customer = null;
            if ($entity->getCustomerId() != null) {
                $customer = $this->customerRepository->getById($entity->getCustomerId());
                foreach (['sap_customer_id', 'slp_code', 'owner_code', 'user_code'] as $attribute) {
                    $attributeObject = $customer->getCustomAttribute($attribute);
                    $attributes[$attribute] = $attributeObject ? $attributeObject->getValue() : null;
                }
            }

            $data = $entity->getData();

            $Authorization = 1;
            $pickup = 'P';
            $serie = 0;
            $groupWarehouse = '';
            $shipToCode = '';
            $docNum = 0;
            $docEntry = 0;
            switch ($type) {
                case self::ORDER_TYPE:
                    $shippingMethod = $entity->getShippingMethod();

                    $paymenTitle = $entity->getPayment()->getMethodInstance()->getTitle();
                    $PaymentCode = $entity->getPayment()->getMethodInstance()->getCode();
                    if ((int)$data['credit_exceeded'] == 1) {
                        $Authorization = 0;
                    }
                    if ($PaymentCode == 'banktransfer') {
                        $Authorization = 2;
                    }
                    $comments = 'siglo21.net #%s pago:%s email:%s';
                    $comments = sprintf($comments, $idMagento, $paymenTitle, $entity->getCustomerEmail());
                    $pickup = (($shippingMethod == "pickup_pickup") ? 'V' : 'P');

                    if ($shippingMethod == "pickup_pickup") {
                        $sapCode = $this->officeRepository->getById((int)$data['pick_up_id']);
                        $officeSAP = trim($sapCode->getSap());
                    }

                    $shipToCode = $this->sap->getAddressSAP($entity->getBillingAddress()->getCustomerAddressId());
                    $serie = $entity->getShippingAddress()->getData('serie');
                    $groupWarehouse = $entity->getShippingAddress()->getData('warehouse_group');
                    break;
                case self::QUOTE_TYPE:
                default:
                    $comments = 'siglo21.net #%s, Titulo: %s';
                    $comments = sprintf($comments, $idMagento, $quote->getName());
                    try {
                        $documentStatus = $this->documentStatusRepository->getByParentId($quote->getId());
                        $docNum = $documentStatus->getSap();
                        $docEntry = $documentStatus->getSapDocEntry();
                    } catch (LocalizedException $e) {
                        $this->logger->debug("Error to get the status with the parent: " . $e->getMessage());
                    }
                    if (!is_null($customer)) {
                        $defaultShipping = $customer->getDefaultShipping();
                        $defaultShippingAddress = $this->addressRepository->getById($defaultShipping);
                        $serie = $this->resolveSerieToQuote($defaultShippingAddress->getCustomAttribute('serie')->getValue());
                        $groupWarehouse = $defaultShippingAddress->getCustomAttribute('warehouse_group')->getValue();
                        $shipToCode = $this->sap->getAddressSAP($defaultShipping);
                    }
                    break;
            }

            $observation = $data['aventi_comment'];
            $observation = (is_string($observation) && $observation != '' && !is_null($observation)) ? $observation : 0;

            /**
             * Gestión para el comentario longitud maxima en SAP 254
             */
            $c = $comments . "\n" . $observation;
            if (strlen($c) > 250) {
                $comments = $observation;
            } else {
                $comments = $c;
            }

            $isListMaterial = $this->validateListMaterial($products);
            $mnjPrice = 'N';
            $ctrlPrice = 'N';
            if ($isListMaterial) {
                $mnjPrice = 'Y';
                $ctrlPrice = 'S';
            }

            $userFields = [
                "U_Despachar~NO",
                "U_Retiro_mercaderia~$pickup",
                "U_DOC_DECLARABLE~S",
                "U_MnjPrice~$mnjPrice",
                "U_ExxVentaWeb~S",
                "U_CtrlPrice~$ctrlPrice",
                "U_ID_Web~$idMagento",
                "U_Coment_Web~$comments"
            ];
            $userFields = trim(implode("|", $userFields));
            $customerIdentification = trim($attributes['sap_customer_id']);
            $slpCode = trim($attributes['slp_code']);
            $ownerCode = trim($attributes['owner_code']);
            $userCode = trim($attributes['user_code']);
            $docDueDate = date('Y-m-d', strtotime($this->dateTime->date('Y-m-d') . ' + ' . $this->configHelper->getDocDueDate() . ' days'));

            $this->dataToSAP = [
                'ObjType' => $type,
                "DocEntry" => $docEntry,
                "DocNum" => $docNum,
                'CardCode' => $customerIdentification,
                "DocDueDate" => $docDueDate,
                "Serie" => $serie,
                "U_G_Bodega" => $groupWarehouse,
                "SlpCode" => $slpCode,//77,
                "OwnerCode" => $ownerCode,
                "User" => $userCode,
                "Autorizado" => $Authorization,
                'CamposUsuario' => $userFields,
                'Detalles' => $products,
                'ShipToCode' => $shipToCode,
                "Comments" => $comments
            ];

            $typeUri = ($operation == QuoteStatusInterface::QUOTE_CREATED) ? 'create_order' : 'update_order';
            return $this->data->postRecourse($typeUri, $this->dataToSAP);
        } catch (\Exception $e) {
            return ['status' => 5001, 'body' => $e->getMessage()];
        }
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @param array $details
     */
    public function updateItemFromQuote($cart, $details)
    {
        foreach ($cart->getAllItems() as $item) {
            foreach ($details['Data'] as $detail) {
                if ($item->getSku() == $detail['ItemCode']) {
                    $lineSap = ((int)$detail['LineNum'] == 0) ? 0 . "" : $detail['LineNum'];
                    $itemStatus = null;
                    try {
                        $itemStatus = $this->itemStatusRepositoryInterface->getByItemId($item->getId());
                    } catch (LocalizedException $e) {
                        $itemStatus = $this->itemStatusInterfaceFactory->create();
                    }
                    if ($itemStatus) {
                        $itemStatus->setBaseEntry($details['DocEntry']);
                        $itemStatus->setBaseType($details['DocType']);
                        $itemStatus->setLineSap($lineSap);
                        $itemStatus->setItemId($item->getId());
                        try {
                            $this->itemStatusRepositoryInterface->save($itemStatus);
                        } catch (LocalizedException $e) {
                            $this->logger->debug("ERROR TO SAVE ITEM STATUS: " . $e->getMessage());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $cartId
     * @return DataObject
     */
    private function getQuoteByCartId($cartId)
    {
        return $this->_quoteCollectionFactory->create()->addFieldToFilter('cart_id', ['eq' => $cartId])->getFirstItem();
    }

    /**
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     */
    private function resolveQuoteDiscount($quote)
    {
        $discount = 0;
        switch ($quote->getNegotiatedDiscountType()) {
            case \Aheadworks\Ctq\Model\Source\Quote\Negotiation\DiscountType::PERCENTAGE_DISCOUNT:
                $discount = (float) $quote->getNegotiatedDiscountValue();
                break;
            case \Aheadworks\Ctq\Model\Source\Quote\Negotiation\DiscountType::AMOUNT_DISCOUNT:
            default:
                $discount = (float) round($quote->getNegotiatedDiscountValue() * 100 / $quote->getBaseQuoteTotal());
                break;
        }

        return $discount;
    }

    /**
     * @param $item
     * @return int
     */
    private function resolveOrderDiscount($item)
    {
        $discount = 0;
        $percent = (int)$item->getData('aw_ctq_percent');
        if ($percent > 0) {
            $discount = $percent;
        }

        return $discount;
    }
}
