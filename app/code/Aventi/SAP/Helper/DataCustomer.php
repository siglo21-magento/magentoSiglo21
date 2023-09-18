<?php

namespace Aventi\SAP\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class DataCustomer extends AbstractHelper
{
    /**
    * @var  CustomerRepositoryInterface
    */
    protected $_customerInterface;

    /**
     * @param Context $context
     * @param CustomerRepositoryInterface $customerInterface
    */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerInterface
    ) {
        parent::__construct($context);
        $this->_customerInterface = $customerInterface;
    }

    /**
     *
     * Get the customer all information
     *
     * @param $customerId
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getInfo($customerId): CustomerInterface
    {
        return $this->_customerInterface->getById($customerId);
    }

    /**
     * Get the seller's email.
     *
     * @param null $customerId
     * @return string|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getSellerEmail($customerId = null): ?string
    {
        $customer = $this->getInfo($customerId);
        if (null !== $customer->getCustomAttribute('seller_email')) {
            return $customer->getCustomAttribute('seller_email')->getValue();
        }
        return 'noreply@siglo21.net';
    }
}
