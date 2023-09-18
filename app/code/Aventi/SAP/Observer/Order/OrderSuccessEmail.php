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
 * By Adrián Olave
 */

namespace Aventi\SAP\Observer\Order;



use Aventi\SAP\Helper\DataCustomer;
use Aventi\SAP\Helper\DataEmail;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class OrderSuccessEmail implements \Magento\Framework\Event\ObserverInterface
{

    private $logger;

    private $dataEmail;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var DataCustomer
     */
    private $_dataCustomer;

    /**
     * @param LoggerInterface $logger
     * @param DataEmail $dataEmail
     * @param OrderRepositoryInterface $orderRepository
     * @param DataCustomer $dataCustomer
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Helper\DataEmail $dataEmail,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        DataCustomer $dataCustomer
    ) {
        $this->logger = $logger;
        $this->dataEmail = $dataEmail;
        $this->orderRepository = $orderRepository;
        $this->_dataCustomer = $dataCustomer;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     * @throws MailException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getEvent()->getOrder();
        if ($order) {
            $this->dataEmail->sendOrderEmail(
                $this->_dataCustomer->getSellerEmail($order->getCustomerId()),
                $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                $order->getIncrementId(),
                'El cliente ha realizado una compra',
                'Información privada',
                'Información privada',
                $order
            );
        }
    }
}
