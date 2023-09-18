<?php

namespace Aventi\UpdateOrderStatus\Model;

class OrderStatus
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $convertOrder;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderFactory = $orderFactory;
        $this->convertOrder = $convertOrder;
        $this->invoiceService = $invoiceService;
        $this->logger = $logger;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changeStatus()
    {
        $processingOrders = $this->getOrdersBySearchCriteria('status', 'processing', 'eq');
        $this->logger->debug("Estoy antes del foreach");
        foreach ($processingOrders->getItems() as $processingOrder) {
            $incrementId = $processingOrder->getIncrementId();
            $orderModel = $this->orderFactory->create();
            $order = $orderModel->loadByIncrementId($incrementId);
            $this->processOrder($order);
        }
    }

    /**
     * @param $order
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processOrder($order)
    {
        $state = $order::STATE_COMPLETE;
        $orderStatus = $order::STATE_COMPLETE;
        $this->logger->debug("Order a procesar:" . $order->getIncrementId());
        if (!$order->hasInvoices()) {
            $this->createOrderInvoice($order);
        }
        if (!$order->hasShipments() && $order->canShip()) {
            $this->createOrderShipment($order);
        }
        //$order->setState($state)->setStatus($orderStatus)->save();
    }

    /**
     * @param $field
     * @param $value
     * @param $condition
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    private function getOrdersBySearchCriteria($field , $value, $condition)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                $field,
                $value,
                $condition
            )->create();
        return $this->orderRepository->getList($searchCriteria);
    }

    /**
     * @param $order
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createOrderShipment($order)
    {
        $itemsToShip = [];
        $sourceCode = 'CDGARZOT';
        $shipment = $this->convertOrder->toShipment($order);
        foreach ($order->getAllItems() AS $orderItem) {
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            $qtyShipped = $orderItem->getQtyToShip();
            $itemsToShip[] = ['sku' => $orderItem->getSku(), 'qtyToShip' => $qtyShipped];
            $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        foreach ($itemsToShip as $item) {
            $this->processStock($item, $sourceCode);
        }

        try {
            $shipment->getExtensionAttributes()->setSourceCode('CDGARZOT');
            $shipment->save();
            $shipment->getOrder()->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param $order
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createOrderInvoice($order)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $transactionFactory = $objectManager->get(\Magento\Framework\DB\TransactionFactory::class);
        $transaction = $transactionFactory->create();
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $invoice->getOrder()->setIsInProcess(true);
        $invoice->pay();
        $invoice->save();

        $transaction->addObject($invoice);
        $transaction->addObject($invoice->getOrder());
        $transaction->save();
    }

    private function processStock(array $itemsToShip, string $sourceCode)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stockInterface = $objectManager->get(\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory::class);
        $stockSaveInterface = $objectManager->get(\Magento\InventoryApi\Api\SourceItemsSaveInterface::class);
        $sourceItem = $stockInterface->create();
        $sourceItem->setSourceCode($sourceCode);
        $sourceItem->setSku($itemsToShip['sku']);
        $sourceItem->setQuantity($itemsToShip['qtyToShip']);
        $sourceItem->setStatus(1);
        $stockSaveInterface->execute([$sourceItem]);
    }
}
