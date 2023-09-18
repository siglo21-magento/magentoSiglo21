<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Quote\Admin\Quote;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\Quote\Negotiation\DiscountType;
use Aheadworks\Ctq\Model\Quote\Discount\Calculator\DiscountCalculatorInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Model\Metadata\Form as CustomerForm;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\DataObject;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater\CtqQuoteSetter;

/**
 * Class Updater
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote
 */
//todo future refactoring
class Updater extends DataObject implements CartInterface
{
    /**
     * Quote session object
     *
     * @var \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote
     */
    protected $_session;

    /**
     * Re-collect quote flag
     *
     * @var boolean
     */
    protected $_needCollect;

    /**
     * Re-collect cart flag
     *
     * @var boolean
     */
    protected $_needCollectCart = false;

    /**
     * Collect (import) data and validate it flag
     *
     * @var boolean
     */
    protected $_isValidate = false;

    /**
     * Array of validate errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Quote associated with the model
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer
     */
    protected $quoteInitializer;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_metadataFormFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Item\Updater
     */
    protected $quoteItemUpdater;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $objectFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CtqQuoteSetter
     */
    private $ctqQuoteSetter;

    /**
     * @var int
     */
    private $itemsCount;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote $quoteSession
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $quoteInitializer
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param Item\Updater $quoteItemUpdater
     * @param DataObject\Factory $objectFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param CtqQuoteSetter $ctqQuoteSetter
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     * @param ExtensibleDataObjectConverter|null $dataObjectConverter
     * @param StoreManagerInterface|null $storeManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Config $salesConfig,
        \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote $quoteSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $quoteInitializer,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        CtqQuoteSetter $ctqQuoteSetter,
        PriceCurrencyInterface $priceCurrency,
        array $data = [],
        ExtensibleDataObjectConverter $dataObjectConverter = null,
        StoreManagerInterface $storeManager = null
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_salesConfig = $salesConfig;
        $this->_session = $quoteSession;
        $this->_logger = $logger;
        $this->quoteInitializer = $quoteInitializer;
        $this->messageManager = $messageManager;
        $this->_metadataFormFactory = $metadataFormFactory;
        $this->quoteItemUpdater = $quoteItemUpdater;
        $this->objectFactory = $objectFactory;
        $this->quoteRepository = $quoteRepository;
        $this->ctqQuoteSetter = $ctqQuoteSetter;
        $this->priceCurrency = $priceCurrency;

        parent::__construct($data);
        $this->dataObjectConverter = $dataObjectConverter ?: ObjectManager::getInstance()
            ->get(ExtensibleDataObjectConverter::class);
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    /**
     * Set validate data in import data flag
     *
     * @param boolean $flag
     * @return $this
     */
    public function setIsValidate($flag)
    {
        $this->_isValidate = (bool)$flag;
        return $this;
    }

    /**
     * Return is validate data in import flag
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidate()
    {
        return $this->_isValidate;
    }

    /**
     * Retrieve quote item
     *
     * @param int|Item $item
     * @return Item|false
     */
    protected function _getQuoteItem($item)
    {
        if ($item instanceof Item) {
            return $item;
        } elseif (is_numeric($item)) {
            return $this->getSession()->getQuote()->getItemById($item);
        }

        return false;
    }

    /**
     * Initialize data for price rules
     *
     * @return $this
     */
    public function initRuleData()
    {
        $this->_coreRegistry->register(
            'rule_data',
            new \Magento\Framework\DataObject(
                [
                    'store_id' => $this->_session->getStore()->getId(),
                    'website_id' => $this->_session->getStore()->getWebsiteId(),
                    'customer_group_id' => $this->getCustomerGroupId()
                ]
            ),
            true
        );

        return $this;
    }

    /**
     * Initialize quote extension attributes
     *
     * @param array $data
     * @return $this
     */
    public function setAwCtqQuoteToCart($data)
    {
        /** todo refactoring to composite updater */
        $this->ctqQuoteSetter->setAwCtqQuoteToCart($this->getQuote(), $data);

        return $this;
    }

    /**
     * Set collect totals flag for quote
     *
     * @param   bool $flag
     * @return $this
     */
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;
        return $this;
    }

    /**
     * Recollect totals for customer cart.
     *
     * Set recollect totals flag for quote.
     *
     * @return $this
     */
    public function recollectCart()
    {
        if ($this->_needCollectCart === true) {
            $this->getCustomerCart()->collectTotals();
            $this->quoteRepository->save($this->getCustomerCart());
        }
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Quote saving
     *
     * @return $this
     */
    public function saveQuote()
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }

        if ($this->_needCollect) {
            $this->getQuote()->setTotalsCollectedFlag(false);
            $this->getQuote()->collectTotals();
        }

        $this->quoteRepository->save($this->getQuote());
        return $this;
    }

    /**
     * Retrieve session model object of quote
     *
     * @return \Aheadworks\Ctq\Model\Quote\Admin\Session\Quote
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Retrieve quote object model
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->getSession()->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Set quote object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Retrieve current customer group ID.
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $groupId = $this->getQuote()->getCustomerGroupId();
        if (!$groupId) {
            $groupId = $this->getSession()->getCustomerGroupId();
        }

        return $groupId;
    }

    /**
     * Move quote item to another items list
     *
     * @param int|Item $item
     * @param string $moveTo
     * @param int $qty
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function moveQuoteItem($item, $moveTo, $qty)
    {
        $item = $this->_getQuoteItem($item);
        if ($item) {
            $removeItem = false;
            $moveTo = explode('_', $moveTo);
            switch ($moveTo[0]) {
                case 'remove':
                    $removeItem = true;
                    break;
                default:
                    break;
            }
            if ($removeItem) {
                $this->getQuote()->deleteItem($item);
            }
            $this->setRecollect(true);
        }

        return $this;
    }

    /**
     * Remove quote item
     *
     * @param int $item
     * @return $this
     */
    public function removeQuoteItem($item)
    {
        $this->getQuote()->removeItem($item);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Add product to current order quote
     * $product can be either product id or product model
     * $config can be either buyRequest config, or just qty
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @param array|float|int|\Magento\Framework\DataObject $config
     * @return $this
     * @throws LocalizedException
     */
    public function addProduct($product, $config = 1)
    {
        if (!is_array($config) && !$config instanceof \Magento\Framework\DataObject) {
            $config = ['qty' => $config];
        }
        $config = new \Magento\Framework\DataObject($config);

        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product;
            $product = $this->_objectManager->create(
                \Magento\Catalog\Model\Product::class
            )->setStore(
                $this->getSession()->getStore()
            )->setStoreId(
                $this->getSession()->getStoreId()
            )->load(
                $product
            );
            if (!$product->getId()) {
                throw new LocalizedException(
                    __('We could not add a product to cart by the ID "%1".', $productId)
                );
            }
        }

        $item = $this->quoteInitializer->init($this->getQuote(), $product, $config);

        if (is_string($item)) {
            throw new LocalizedException(__($item));
        }
        $item->checkData();
        $item->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_RESET);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Add multiple products to current order quote
     *
     * @param array $products
     * @return $this
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (double)$config['qty'] : 1;
            try {
                $this->addProduct($productId, $config);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                return $e;
            }
        }

        return $this;
    }

    /**
     * Parse shipping data retrieved from request
     *
     * @param array $data
     * @return $this
     */
    public function importShippingInformation($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);

            if ($this->isEmptyBillingAddress()) {
                $this->setBillingAddress($data['shipping_address']);
            }
        }

        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }

        return $this;
    }

    /**
     * Check if billing address is empty
     *
     * @return bool
     */
    private function isEmptyBillingAddress()
    {
        $billing = $this->getBillingAddress();

        return empty($billing->getFirstname()) || empty($billing->getLastname())
            || empty($billing->getCity()) || empty($billing->getRegion());
    }

    /**
     * Update quantity of order quote items
     *
     * @param array $items
     * @return $this
     * @throws \Exception|LocalizedException
     */
    public function updateQuoteItems($items)
    {
        if (!is_array($items)) {
            return $this;
        }

        try {
            $this->resetCtqQuote();
            foreach ($items as $itemId => $info) {
                if (!empty($info['configured'])) {
                    unset($info['proposed_price']);
                    $item = $this->getQuote()->updateItem($itemId, $this->objectFactory->create($info));
                    $info['qty'] = (double)$item->getQty();
                } else {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item) {
                        continue;
                    }
                    $info['qty'] = (double)$info['qty'];
                }
                $this->quoteItemUpdater->update($item, $info);
                if ($item && !empty($info['action'])) {
                    $this->moveQuoteItem($item, $info['action'], $item->getQty());
                }

                $this->calculateItemPrice($item, $info);
            }
        } catch (LocalizedException $e) {
            $this->recollectCart();
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        $this->recollectCart();

        return $this;
    }

    /**
     * Reset negotiated discount value
     */
    private function resetCtqQuote()
    {
        if ($this->getQuote()->getExtensionAttributes()
            && $this->getQuote()->getExtensionAttributes()->getAwCtqQuote()
        ) {
            $this->getQuote()->getExtensionAttributes()->getAwCtqQuote()->setNegotiatedDiscountValue(0);
        }
    }

    /**
     * Reset calculate flag
     */
    public function resetItems()
    {
        $items = $this->getQuote()->getAllItems();
        foreach ($items as $item) {
            $item->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_DEFAULT);
        }
    }

    /**
     * Return valid price
     *
     * @param float|int $price
     * @return float|int
     */
    protected function _parseCustomPrice($price)
    {
        $price = $this->_objectManager->get(\Magento\Framework\Locale\FormatInterface::class)->getNumber($price);
        $price = $price > 0 ? $price : 0;

        return $price;
    }

    /**
     * Retrieve order quote shipping address
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Set and validate Quote address
     *
     * All errors added to _errors
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param array $data
     * @return $this
     */
    protected function _setQuoteAddress(\Magento\Quote\Model\Quote\Address $address, array $data)
    {
        $isAjax = !$this->getIsValidate();

        // Region is a Data Object, so it is represented by an array. validateData() doesn't understand arrays, so we
        // need to merge region data with address data. This is going to be removed when we switch to use address Data
        // Object instead of the address model.
        // Note: if we use getRegion() here it will pull region from db using the region_id
        $data = isset($data['region']) && is_array($data['region']) ? array_merge($data, $data['region']) : $data;

        $addressForm = $this->_metadataFormFactory->create(
            
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            'adminhtml_customer_address',
            $data,
            $isAjax,
            CustomerForm::DONT_IGNORE_INVISIBLE,
            []
        );

        // prepare request
        // save original request structure for files
        if ($address->getAddressType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
            $requestData = ['order' => ['shipping_address' => $data]];
            $requestScope = 'order/shipping_address';
        } else {
            $requestData = ['order' => ['billing_address' => $data]];
            $requestScope = 'order/billing_address';
        }
        $request = $addressForm->prepareRequest($requestData);
        $addressData = $addressForm->extractData($request, $requestScope);
        if ($this->getIsValidate()) {
            $errors = $addressForm->validateData($addressData);
            if ($errors !== true) {
                if ($address->getAddressType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
                    $typeName = __('Shipping Address: ');
                } else {
                    $typeName = __('Billing Address: ');
                }
                foreach ($errors as $error) {
                    $this->_errors[] = $typeName . $error;
                }
                $address->setData($addressForm->restoreData($addressData));
            } else {
                $address->setData($addressForm->compactData($addressData));
            }
        } else {
            $address->addData($addressForm->restoreData($addressData));
        }

        return $this;
    }

    /**
     * Set shipping address into quote
     *
     * @param \Magento\Quote\Model\Quote\Address|array $address
     * @return $this
     */
    public function setShippingAddress($address)
    {
        if (is_array($address)) {
            $shippingAddress = $this->_objectManager->create(
                \Magento\Quote\Model\Quote\Address::class
            )->setData(
                $address
            )->setAddressType(
                \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING
            );
            if (!$this->getQuote()->isVirtual()) {
                $this->_setQuoteAddress($shippingAddress, $address);
            }
        }
        if ($address instanceof \Magento\Quote\Model\Quote\Address) {
            $shippingAddress = $address;
        }

        $this->setRecollect(true);
        $this->getQuote()->setShippingAddress($shippingAddress);

        return $this;
    }

    /**
     * Set shipping address to be same as billing
     *
     * @param bool $flag If true - don't save in address book and actually copy data across billing and shipping
     *                   addresses
     * @return $this
     */
    public function setShippingAsBilling($flag)
    {
        if ($flag) {
            $tmpAddress = clone $this->getBillingAddress();
            $tmpAddress->unsAddressId()->unsAddressType();
            $data = $tmpAddress->getData();
            $data['save_in_address_book'] = 0;
            // Do not duplicate address (billing address will do saving too)
            $this->getShippingAddress()->addData($data);
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Retrieve quote billing address
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    /**
     * Set billing address into quote
     *
     * @param array $address
     * @return $this
     */
    public function setBillingAddress($address)
    {
        if (!is_array($address)) {
            return $this;
        }

        $billingAddress = $this->_objectManager->create(Address::class)
            ->setData($address)
            ->setAddressType(Address::TYPE_BILLING);

        $this->_setQuoteAddress($billingAddress, $address);

        /**
         * save_in_address_book is not a valid attribute and is filtered out by _setQuoteAddress,
         * that is why it should be added after _setQuoteAddress call
         */
        $saveInAddressBook = (int)(!empty($address['save_in_address_book']));
        $billingAddress->setData('save_in_address_book', $saveInAddressBook);

        $quote = $this->getQuote();
        if (!$quote->isVirtual() && $this->getShippingAddress()->getSameAsBilling()) {
            $address['save_in_address_book'] = 0;
            $this->setShippingAddress($address);
        }

        // not assigned billing address should be saved as new
        // but if quote already has the billing address it won't be overridden
        if (empty($billingAddress->getCustomerAddressId())) {
            $billingAddress->setCustomerAddressId(null);
            $quote->getBillingAddress()->setCustomerAddressId(null);
        }
        $quote->setBillingAddress($billingAddress);

        return $this;
    }

    /**
     * Set shipping method
     *
     * @param string $method
     * @return $this
     */
    public function setShippingMethod($method)
    {
        $this->getShippingAddress()->setShippingMethod($method);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Empty shipping method and clear shipping rates
     *
     * @return $this
     */
    public function resetShippingMethod()
    {
        $this->getShippingAddress()->setShippingMethod(false);
        $this->getShippingAddress()->removeAllShippingRates();

        return $this;
    }

    /**
     * Collect shipping data for quote shipping address
     *
     * @return $this
     */
    public function collectShippingRates()
    {
        $store = $this->getQuote()->getStore();
        $this->storeManager->setCurrentStore($store);
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->collectRates();

        return $this;
    }

    /**
     * Calculate totals
     *
     * @return void
     */
    public function collectRates()
    {
        $this->getQuote()->collectTotals();
    }

    /**
     * Calculate Item Price
     *
     * @param Item $item
     * @param array $itemInfo
     * @throws LocalizedException
     */
    private function calculateItemPrice($item, $itemInfo)
    {
        if (isset($itemInfo['proposed_price'])) {
            $this->updatePricePerProduct($item, $itemInfo['proposed_price']);
        } else {
            $item->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_RESET);
            $ctqQuote = $this->getQuote()->getExtensionAttributes()->getAwCtqQuote();
            if ($ctqQuote->getNegotiatedDiscountType() == DiscountType::PERCENTAGE_DISCOUNT) {
                $ctqQuote->setNegotiatedDiscountValue(
                    $this->priceCurrency->round($ctqQuote->getNegotiatedDiscountValue() / ++$this->itemsCount)
                );
            }
        }
    }

    /**
     * Update proposed price per product
     *
     * @param Item $item
     * @param float $proposedPrice
     * @throws LocalizedException
     */
    private function updatePricePerProduct($item, $proposedPrice)
    {
        if ($proposedPrice < 0 || $item->getBasePrice() < $proposedPrice) {
            throw new LocalizedException(
                __('Proposed price cannot be less than 0 or higher than catalog price.')
            );
        }

        if ($this->getQuote()->getExtensionAttributes()
            && !$this->getQuote()->getExtensionAttributes()->getAwCtqQuote()
        ) {
            $this->setAwCtqQuoteToCart(
                [
                    'quote' =>
                        [
                            QuoteInterface::NEGOTIATED_DISCOUNT_TYPE => DiscountType::PERCENTAGE_DISCOUNT
                        ]
                ]
            );
        }

        if (!$item->isDeleted()) {
            $amount = $item->getPrice() * $item->getQty() - ($proposedPrice * $item->getQty());
            $baseAmount = $item->getBasePrice() * $item->getQty() - ($proposedPrice * $item->getQty());
            $percent = $amount * 100 / ($item->getBasePrice() * $item->getQty());
            $this->itemsCount++;
        } else {
            $amount = 0;
            $baseAmount = 0;
            $percent = 0;
            $proposedPrice = 0;
        }

        /** @var QuoteInterface $ctqQuote */
        $ctqQuote = $this->getQuote()->getExtensionAttributes()->getAwCtqQuote();
        $negotiatedType = $ctqQuote->getNegotiatedDiscountType();
        $negotiatedValue = $ctqQuote->getNegotiatedDiscountValue();

        if ($negotiatedType == null) {
            $negotiatedType = DiscountType::PERCENTAGE_DISCOUNT;
            $ctqQuote->setNegotiatedDiscountType($negotiatedType);
        }

        switch ($negotiatedType) {
            case DiscountType::PERCENTAGE_DISCOUNT:
                //Workaround for calculate average discount percent in quote
                if ($this->itemsCount == $this->getQuoteItemsCount()) {
                    $newNegotiatedValue = $this->priceCurrency->round(
                        ($negotiatedValue + $percent) / $this->itemsCount
                    );
                } else {
                    $newNegotiatedValue = $negotiatedValue + $percent;
                }
                break;

            case DiscountType::PROPOSED_PRICE:
                $newNegotiatedValue = $this->priceCurrency->round(
                    $negotiatedValue + ($proposedPrice * $item->getQty())
                );
                break;

            case DiscountType::AMOUNT_DISCOUNT:
            default:
                $newNegotiatedValue = $negotiatedValue + $amount;
                break;
        }

        $ctqQuote
            ->setNegotiatedDiscountValue($newNegotiatedValue)
            ->setRecollect(true);

        $item
            ->setAwCtqAmount($amount)
            ->setBaseAwCtqAmount($baseAmount)
            ->setAwCtqPercent($percent)
            ->setAwCtqCalculateType(DiscountCalculatorInterface::CALCULATE_PER_ITEM);

        $this
            ->getQuote()
            ->getExtensionAttributes()
            ->setAwCtqQuote($ctqQuote);
    }

    /**
     * Get quote items count not including deleted ones for right percent discount calculation
     *
     * @return int
     */
    private function getQuoteItemsCount()
    {
        $result = 0;
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            if ($item->isDeleted()) {
                continue;
            }
            $result++;
        }

        return $result;
    }
}
