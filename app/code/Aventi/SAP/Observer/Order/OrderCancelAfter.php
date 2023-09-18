<?php
/**
 * Add file comment to orders
 * Copyright (C) 2018  
 * 
 * This file is part of Aventi/OrderComment.
 * 
 * Aventi/OrderComment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aventi\SAP\Observer\Order;



class OrderCancelAfter implements \Magento\Framework\Event\ObserverInterface
{

    private $logger;

    private $dataEmail;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Aventi\SAP\Helper\DataCustomer
     */
    private $_dataCustomer;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Aventi\SAP\Helper\DataEmail  $dataEmail
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param Aventi\SAP\Helper\DataCustomer $dataCustomer
     * 
    */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Helper\DataEmail $dataEmail,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Aventi\SAP\Helper\DataCustomer $dataCustomer
    )
    {
        $this->logger = $logger;
        $this->dataEmail = $dataEmail;
        $this->orderRepository = $orderRepository;
        $this->_dataCustomer = $dataCustomer;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getEvent()->getOrder();
        if($order){
            $this->dataEmail->sendOrderCancelEmail(
                $order->getCustomerEmail(),                                
                $order->getCustomerFirstname().' '.$order->getCustomerLastname(),
                $order->getIncrementId(),
                $order->getPayment()->getMethodInstance()->getTitle(),
                $order,
                $this->_dataCustomer->getSellerEmail($order->getCustomerId())  
            );
            $order->setData('sap_notification_send',1);
            $this->orderRepository->save($order);
        }        
        
    }
}
