<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Customer\Export;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;

/**
 * Class Address
 * @package Aheadworks\Ctq\ViewModel\Customer
 */
class Address implements ArgumentInterface
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressConfig
     */
    private $addressConfig;

    /**
     * @var Mapper
     */
    private $addressMapper;

    /**
     * @param AddressRepositoryInterface $addressRepository
     * @param CurrentCustomer $currentCustomer
     * @param AddressConfig $addressConfig
     * @param Mapper $addressMapper
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        CurrentCustomer $currentCustomer,
        AddressConfig $addressConfig,
        Mapper $addressMapper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->addressRepository = $addressRepository;
        $this->addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;
    }

    /**
     * Render address
     *
     * @return string
     */
    public function getAddressHtml()
    {
        try {
            $customer = $this->currentCustomer->getCustomer();
            $address = $this->addressRepository->getById($customer->getDefaultBilling());
            $renderer = $this->addressConfig->getFormatByCode('html')->getRenderer();

            return $renderer->renderArray($this->addressMapper->toFlatArray($address));
        } catch (\Exception $e) {
            return '';
        }
    }
}
