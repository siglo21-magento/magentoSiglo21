<?php

namespace Aventi\PriceList\Model\Session;

/**
 * Class Storage
 * @package Aventi\PriceList\Model\Session
 */
class Storage extends \Magento\Framework\Session\Storage
{
    /**
     * Storage constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string $namespace
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $namespace = 'pricelistsession',
        array $data = []
    ) {
        parent::__construct($namespace, $data);
    }
}
