<?php

namespace Aventi\SAP\Model\Sync;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Aventi\SAP\Helper\Attribute;
use Aventi\SAP\Logger\Logger;
use Aventi\SAP\Model\Integration;
use Bcn\Component\Json\Exception\ReadingError;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Math\Random;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

class Customer extends Integration
{

    private $resTable = [
        'check' => 0,
        'fail' => 0,
        'new' => 0,
        'updated' => 0
    ];

    /**
     * @var \Aventi\SAP\Helper\Data
     */
    private $data;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $_customerRepository;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptorInterface;

    /**
     * @var \Aventi\SAP\Helper\Attribute
     */
    private $attributeDate;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    private $dataAddressFactory;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $helperSAP;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    private $customerInterfaceFactory;

    /**
     * @var \Aventi\SAP\Helper\DataEmail
     */
    private $dataEmail;

    private $regionFactory;

    /**
     * @var \Aheadworks\Ca\Api\CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var \Aheadworks\Ca\Model\Company\CompanyManagement
     */
    private $companyManagement;

    /**
     * @var \Aheadworks\Ca\Model\Service\CompanyUserService
     */
    private $companyUserService;

    /**
     * @var \Aheadworks\Ca\Api\Data\CompanyInterfaceFactory
     */
    private $companyInterfaceFactory;

    /**
     * @var \Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement
     */
    private $summaryManagment;

    /**
     * @var \Aheadworks\CreditLimit\Api\SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var \Aheadworks\CreditLimit\Api\Data\SummaryInterfaceFactory
     */
    private $summaryInterfaceFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $_timezone;

    /**
     * @var ExtensionAttributesBuilder
     */
    private $companyUserExtensionAttributesBuilder;

    /**
     * @var Random
     */
    private $_mathRandom;

    /**
     * @var \Aventi\SAP\Model\Check
     */
    private $_check;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $_filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $_filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $_searchCriteriaBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    public function __construct(
        Attribute $attribute,
        Logger $logger,
        DriverInterface $driver,
        Filesystem $filesystem,
        \Aventi\SAP\Helper\Data $data,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        \Aventi\SAP\Helper\Attribute $attributeDate,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $dataAddressFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Aventi\SAP\Helper\SAP $helperSAP,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \Aventi\SAP\Helper\DataEmail $dataEmail,
        \Magento\Directory\Model\Region $region,
        \Aheadworks\Ca\Api\Data\CompanyInterfaceFactory $companyInterfaceFactory,
        \Aheadworks\Ca\Api\CompanyRepositoryInterface $companyRepository,
        \Aheadworks\CreditLimit\Api\Data\SummaryInterfaceFactory $summaryInterfaceFactory,
        \Aheadworks\CreditLimit\Api\SummaryRepositoryInterface $summaryRepository,
        \Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement $summaryManagment,
        \Aheadworks\Ca\Model\Company\CompanyManagement $companyManagement,
        \Aheadworks\Ca\Model\Service\CompanyUserService $companyUserService,
        ExtensionAttributesBuilder $extensionAttributesBuilder,
        \Magento\Framework\Math\Random $random,
        \Aventi\SAP\Model\Check $check,
        \Magento\Framework\Stdlib\DateTime\DateTime $timezone,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($attribute, $logger, $driver, $filesystem);
        $this->data = $data;
        $this->encryptorInterface = $encryptorInterface;
        $this->attributeDate = $attributeDate;
        $this->dataAddressFactory = $dataAddressFactory;
        $this->addressRepository = $addressRepository;
        $this->helperSAP = $helperSAP;
        $this->_customerRepository = $customerRepository;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->regionFactory = $region;
        $this->dataEmail = $dataEmail;
        $this->companyRepository = $companyRepository;
        $this->companyManagement = $companyManagement;
        $this->companyUserService = $companyUserService;
        $this->companyInterfaceFactory = $companyInterfaceFactory;
        $this->summaryManagment = $summaryManagment;
        $this->summaryRepository = $summaryRepository;
        $this->summaryInterfaceFactory = $summaryInterfaceFactory;
        $this->companyUserExtensionAttributesBuilder = $extensionAttributesBuilder;
        $this->_mathRandom = $random;
        $this->_check = $check;
        $this->_timezone = $timezone;
        $this->_filterBuilder = $filterBuilder;
        $this->_filterGroupBuilder = $filterGroupBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param $fast
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws ReadingError
     */
    public function managerCustomerAddress($fast)
    {
        $start = 0;
        $rows = 500;
        $flag = true;

        while ($flag) {
            $jsonData = $this->data->getResource("address", $start, $rows, $fast);
            $jsonPath = $this->getJsonPath($jsonData, "address");
            if ($jsonPath) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter("", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $customersAddress = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($customersAddress as $address) {
                    $addressObject = (object) [
                        "card_code" => $address['CardCode'],
                        "address" => $address['Address'],
                        "street" => $address['Street'],
                        "address_type" => $address['AdresType'],
                        "state" => $address['State'],
                        "city" => $address['City'],
                        "phone" => $address['Phone1'],
                        "option" => true,
                        "c_child" => false,
                        "serie" => $address['Serie'],
                        "u_g_bodega" => $address['U_G_Bodega']
                    ];
                    $this->managerAddress($addressObject);
                    $this->advanceProgressBar($progressBar);
                    //Debug Only.
                    //$total--;
                }
                $this->closeFile($jsonPath);
                $this->finishProgressBar($progressBar, $start, $rows);
                $start += $rows;
                $progressBar = null;
                if ($total <= 0) {
                    $flag = false;
                }
            } else {
                $flag = false;
            }
        }
        $this->printTable($this->resTable);
    }

    /**
     * Customer address management.
     *
     * @param $addressObject
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function managerAddress($addressObject)
    {
        $city = $addressObject->city ?: 'QUITO';
        $street = $addressObject->street ?: 'SIN DIRECCIÓN';
        $postalCode = $this->helperSAP->getPostalCodeByCity($city);
        $arrCustomersId = ($addressObject->c_child) ?: $this->helperSAP->getCustomerArrIds($addressObject->card_code);
        $phone = (is_null($addressObject->phone) ? 'SN' : $addressObject->phone);
        $groupAndSerie = !(is_null($addressObject->serie) || is_null($addressObject->u_g_bodega));

        if (!is_array($arrCustomersId)) {
            $arrCustomersId = [0 => ['entity_id' => $arrCustomersId]];
        }
        foreach ($arrCustomersId as $customerId) {
            $customerId = $customerId['entity_id'];
            if (is_numeric($customerId) && $postalCode && $groupAndSerie && $addressObject->address_type == 'S') {
                $customer = $this->_customerRepository->getById($customerId);
                $name = $customer->getFirstname();
                if (!$addressObject->option) {
                    $customerParent = $this->_customerRepository
                        ->getById($this->helperSAP->getCustomerId($addressObject->card_code));
                    $name = $customerParent->getFirstname();
                }
                $addressId = $this->helperSAP->managerCustomerAddressSAP($addressObject->address, $customerId);
                if (is_numeric($addressId)) {
                    $customerAddress = $this->addressRepository->getById($addressId);
                    $this->resTable['updated']++;
                } else {
                    $customerAddress = $this->dataAddressFactory->create();
                    $this->resTable['new']++;
                }
                $regionId = 0;
                $regionParent = $addressObject->state;
                if (!is_null($addressObject->state)) {
                    $region = $this->regionFactory->loadByCode($addressObject->state, 'EC');
                    $regionId = $region->getId();
                } else {
                    $region = $this->helperSAP->getRegionByCity($city);
                    $region = $this->regionFactory->load($region);
                    if ($region) {
                        $regionId = $region->getId();
                    }
                }
                // load region on the
                // basis of state name and country id

                $customerAddress->setCustomerId($customerId)
                    ->setFirstname($name)
                    ->setLastname('.')
                    ->setCountryId('EC')
                    //->setRegion($region)
                    ->setRegionId($regionId)
                    ->setPostcode($postalCode)
                    ->setCity($city)
                    ->setTelephone($phone)
                    ->setIsDefaultShipping(1)
                    ->setIsDefaultBilling(1)
                    ->setStreet(['0' => $street]);

                $customerAddress->setCustomAttribute('warehouse_group', $addressObject->u_g_bodega);
                $customerAddress->setCustomAttribute('serie', $addressObject->serie);
                try {
                    $this->addressRepository->save($customerAddress);
                    $this->helperSAP
                        ->managerCustomerAddressSAP($addressObject->address, null, $customerAddress->getId());
                } catch (LocalizedException $e) {
                    $this->logger->error("An error has occurred creating address: " . $e->getMessage());
                    $this->resTable['fail']++;
                }
                if ($addressObject->option) {
                    $customerChilds = $this->companyUserService->getChildUsers($customerId);

                    if ($customerChilds) {
                        foreach ($customerChilds as $childs) {
                            $addressObject->option = false;
                            $addressObject->c_child = $childs->getId();
                            $addressObject->state = $regionParent;
                            $this->managerAddress($addressObject);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param bool $fast
     * @return void
     * @throws ReadingError
     * @throws FileSystemException
     */
    public function customer(bool $fast = false)
    {
        $start = 0;
        $rows = 1000;
        $flag = true;
        while ($flag) {
            $jsonData = $this->data->getResource('customer', $start, $rows, $fast);
            $jsonPath = $this->getJsonPath($jsonData, 'customer');
            if ($jsonPath) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter('', \Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $customers = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($customers as $customer) {
                    $customerObject = (object) [
                        'sap_customer_id' => $customer['CardCode'],
                        'email' => $customer['Mail'],
                        'name' => $customer['CardName'],
                        'group_id' => $customer['GroupCode'],
                        'group_name' => $customer['GroupName'],
                        'phone' => $customer['Telefono'],
                        'type_customer' => $customer['U_clas_clientes1']
                    ];
                    $this->managerCustomer($customerObject);
                    $this->advanceProgressBar($progressBar);
                    // Debug only
                    //$total--;
                }
                $start += $rows;
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
                $this->closeFile($jsonPath);
                if ($total <= 0) {
                    $flag = false;
                }
            } else {
                $flag = false;
            }
        }
        $this->printTable($this->resTable);
    }

    /**
     * @param bool $fast
     * @return void
     * @throws FileSystemException
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     * @throws ReadingError
     */
    public function company(bool $fast)
    {
        $start = 0;
        $rows = 1000;
        $flag = true;

        while ($flag) {
            $jsonData = $this->data->getResource('customer', $start, $rows, $fast);
            $jsonPath = $this->getJsonPath($jsonData, 'customer');
            if (is_string($jsonPath)) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter("", \Bcn\Component\Json\Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $customers = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($customers as $customer) {
                    $customerObject = (object) [
                        'card_code' => $customer['CardCode'],
                        'status' => $this->getStatus($customer['frozenFor']),
                        'first_name' => $customer['CardName'],
                        'last_name' => $customer['CardFName'],
                        'legal_name' => $customer['CardName'],
                        'ruc' => $customer['LicTradNum'],
                        'email' => $this->getEmailFormatted($customer['E_Mail']),
                        'street' => $customer['Street'] ?? 'N/A',
                        'city' => $customer['City'],
                        'state' => $customer['State'],
                        'region' => '',
                        'region_id' => '',
                        'postcode' => '',
                        'phone' => $customer['Phone1'],
                        'group_code' => $customer['GroupCode'],
                        'customer_balance' => $customer['Balance'],
                        'customer_credit_line' => $customer['CreditLine'],
                        'slp_code' => $customer['SlpCode'],
                        'employee_id' => $customer['empID'],
                        'user_code' => $customer['User_Code'],
                        'ship_code' => $customer['ShipToDef'],
                        'list_num' => $customer['ListNum'],
                        'seller_email' => $customer['email'] ?? ''
                    ];
                    $this->managerCompany($customerObject);
                    $this->advanceProgressBar($progressBar);
                    // Debug only
                    //$total--;
                    //sleep(5);
                }
                $start += $rows;
                $this->finishProgressBar($progressBar, $start, $rows);
                $progressBar = null;
                $this->closeFile($jsonPath);
                if ($total <= 0) {
                    $flag = false;
                }
            } else {
                $flag = false;
            }
        }
        $this->printTable($this->resTable);
    }

    /**
     * Register and Updated customer
     *
     * @param $customerObject
     */
    public function managerCustomer($customerObject)
    {
        if (filter_var($customerObject->email, FILTER_VALIDATE_EMAIL)) {
            try {
                $customer = $this->_customerRepository->get($customerObject->email);
                $resultCheck = $this->_check->checkData($customerObject, $customer, 'customer');
                if (!$resultCheck) {
                    $this->resTable['check']++;
                } else {
                    $customer->setFirstname($customerObject->name);
                    /*$customer->setStoreId(1);
                    $customer->setWebsiteId(1);*/
                    $customer->setCustomAttribute('sap_customer_id', $customerObject->sap_customer_id);
                    $customer->setCustomAttribute('type_customer', $customerObject->typeCustomer);
                    $this->_customerRepository->save($customer);
                    $this->resTable['updated']++;
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->createCustomer($customerObject);
            } catch (\Exception $e) {
                $this->resTable['fail']++;
                $this->logger->error("An error has occurred: " . $e->getMessage());
            }
        }
    }

    /**
     * @param $customerObject
     * @return void
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     */
    public function managerCompany($customerObject)
    {
        if (filter_var($customerObject->email, FILTER_VALIDATE_EMAIL)) {
            $companyId = $this->helperSAP->managerCompanySAP($customerObject->card_code);

            $customerObject->city = (strpos($customerObject->city, '?') !== false) ? 'QUITO' : $customerObject->city;
            $customerObject->city = (is_null($customerObject->city)) ? "QUITO" : $customerObject->city;
            $region = $this->getRegion($customerObject->state, $customerObject->city);

            $customerObject->region = $region->getName();
            $customerObject->region_id = $region->getId();
            $customerObject->postcode = $this->helperSAP
                ->getPostalCode($region->getName(), $customerObject->city) ?? '000000';
            $customerObject->list_num = $this->attributeDate->resolveGroup($customerObject->list_num);
            $adminCustomer = $this->createAdminCustomer($customerObject);
            if (!$adminCustomer) {
                return;
            }
            try {
                $company = $this->companyRepository->get($companyId);
                $resultCheck = $this->_check->checkData($customerObject, $company, 'company');
                if (!$resultCheck) {
                    $this->resTable['check']++;
                } else {
                    foreach ($resultCheck as $key => $value) {
                        $company->setData($key, $value);
                    }
                    $this->companyUserExtensionAttributesBuilder->setAwCompanyUserIfNotIsset($adminCustomer);
                    $company = $this->companyManagement->updateCompany($company, $adminCustomer);
                    $this->resTable['updated']++;
                }
            } catch (NoSuchEntityException $e) {
                $company = $this->createCompany($customerObject, $adminCustomer);
                $this->resTable['new']++;
            }
            if ($company) {
                try {
                    $this->helperSAP->managerCompanySAP($customerObject->card_code, $company->getId());
                    $companyId = $this->helperSAP->managerCompanySAP($customerObject->card_code);
                    $company = $this->companyRepository->get($companyId);
                    $this->setDefaultShipping($adminCustomer->getId(), $customerObject->ship_code);
                    $this->managerSummary(
                        $adminCustomer->getId(),
                        $company->getId(),
                        $customerObject->customer_balance,
                        $customerObject->customer_credit_line
                    );
                } catch (NoSuchEntityException | LocalizedException $e) {
                    $this->resTable['fail']++;
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }

    /**
     * @param $customerObject
     * @return CustomerInterface|false
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     */
    public function createAdminCustomer($customerObject)
    {
        $customerObject->last_name = (is_null($customerObject->last_name) ? '. ' : $customerObject->last_name . ' .');

        try {
            $customer = $this->getCustomerByAttribute($customerObject->card_code);
            $resultCheck = $this->_check->checkData($customerObject, $customer, 'customer');
            if (!$resultCheck) {
                $this->resTable['check']++;
            } else {
                $customer->setEmail($customerObject->email);
                $customer->setFirstname($customerObject->first_name);
                $customer->setLastName($customerObject->last_name);
                $customer->setGroupId($customerObject->list_num);
                $customer->setCustomAttribute('sap_customer_id', $customerObject->card_code);
                $customer->setCustomAttribute('slp_code', $customerObject->slp_code);
                $customer->setCustomAttribute('identification_customer', $customerObject->ruc);
                $customer->setCustomAttribute('owner_code', $customerObject->employee_id);
                $customer->setCustomAttribute('user_code', $customerObject->user_code);
                $customer->setCustomAttribute('seller_email', $customerObject->seller_email);
                $customer = $this->_customerRepository->save($customer);
                $this->resTable['updated']++;
            }
            //$customer->setCustomAttribute('type_customer', $typeCustomer);
            //$customer = $this->customerRepository->save($customer);
        } catch (NoSuchEntityException | LocalizedException $e) {
            $customer = $this->createCustomer($customerObject);
            $this->resTable['new']++;
        }

        return $customer;
    }

    public function managerSummary($customerId, $companyId, $creditBalance, $creditLimit)
    {
        $date = $this->_timezone->gmtDate();
        $availableCredit = (($creditLimit) - ($creditBalance));
        $creditBalance = $creditBalance * -1;

        try {
            $summary = $this->summaryRepository->getByCustomerId($customerId);
            $summary->setCustomerId($customerId);
            $summary->setCompanyId($companyId);
            $summary->setCreditLimit($creditLimit);
            //$summary->setWebsiteId(1);
            $summary->setCreditBalance($creditBalance);
            $summary->setCreditAvailable($availableCredit);
            $summary->setLastPaymentDate($date);
            $summary->setCurrency('USD');
            $this->summaryManagment->saveCreditSummary($summary);
        } catch (NoSuchEntityException $e) {
            $summary = $this->summaryInterfaceFactory->create();
            $summary->setCustomerId($customerId);
            $summary->setCompanyId($companyId);
            $summary->setCreditLimit($creditLimit);
            /*$summary->setWebsiteId(1);*/
            $summary->setCreditBalance($creditBalance);
            $summary->setCreditAvailable($availableCredit);
            $summary->setLastPaymentDate($date);
            $summary->setCurrency('USD');
            $this->summaryManagment->saveCreditSummary($summary);
        }
    }

    /**
     * @param $customerId
     * @param $shippingDefault
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setDefaultShipping($customerId, $shippingDefault)
    {
        $addressId = $this->helperSAP->managerCustomerAddressSAP($shippingDefault, $customerId);
        if ($addressId) {
            $customerAddress = $this->addressRepository->getById($addressId);
            $customerAddress->setIsDefaultShipping(true);
            try {
                $this->addressRepository->save($customerAddress);
            } catch (\Exception $e) {
                $this->logger->error("Error occurred assign default shipping address: " . $e->getMessage());
            }
        }
    }

    /**
     * Returns Invoice Data from SAP, depending on Customer ID.
     * @param $cardCode
     * @return array
     */
    public function getInvoiceByCustomer($cardCode): array
    {
        $method = 'api/Documento/FacturasPorCliente/';
        $result = json_decode($this->data->getResourceCustom($method, $cardCode), true);

        $return = [];
        if (is_array($result) && isset($result['data'])) {
            foreach ($result['data'] as $res) {
                $return[] = [
                    "DocEntry" => $res['DocEntry'],
                    "DocNum" => $res['DocNum'],
                    "DocDate" => $this->data->formatDateSAP($res['DocDate']),
                    "DocStatus" => $this->data->formatStatus($res['DocStatus'], $res['CANCELED']),
                    "DocTotal" => $this->data->formatDocTotal($res['DocTotal']),
                    "Canceled" => $res['CANCELED']
                ];
            }
        }
        return $return;
    }

    /**
     * @param $customerObject
     * @return CustomerInterface|boolean
     */
    private function createCustomer($customerObject)
    {
        $customer = $this->customerInterfaceFactory->create();
        $storeId = $this->_storeManager->getStore()->getId();
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $customer->setStoreId($storeId);
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($customerObject->email);
        $customer->setFirstname($customerObject->first_name);
        $customer->setLastname($customerObject->last_name);
        $customer->setGroupId($customerObject->list_num);
        $customer->setCustomAttribute('sap_customer_id', $customerObject->card_code);
        $customer->setCustomAttribute('slp_code', $customerObject->slp_code);
        $customer->setCustomAttribute('identification_customer', $customerObject->ruc);
        $customer->setCustomAttribute('owner_code', $customerObject->employee_id);
        $customer->setCustomAttribute('user_code', $customerObject->user_code);
        $customer->setCustomAttribute('seller_email', $customerObject->seller_email);

        // Comentado temporalmente.
        //$password = $this->randomPassword();
        //$this->_customerRepository->save($customer, $this->encryptorInterface->getHash($password, true));
        //$this->dataEmail->sendEmail($customerObject->email, $customerObject->name, $password);

        try {
            $customer = $this->_customerRepository->save($customer);
        } catch (LocalizedException $e) {
            if ($e->getMessage() === "A customer with the same email address already exists in an associated website.") {
                $customer = false;
            }
        }

        return $customer;

        /* save address of customer

        $address = $this->_dataAddressFactory->create();
        $address->setFirstname($customerObject->firstname);
        $address->setLastname($lastName);
        $address->setTelephone($customerObject->phone);

        $street[] = $customerObject->street;//pass street as array
        $address->setStreet($street);

        $address->setCity($customerObject->city);
        $address->setCountryId($customerObject->country);
        $address->setCustomAttribute('sap_address_type', $customerObject->address);
        $address->setVatId($customerObject->taxvat);
        $address->setPostcode($customerObject->postcode); // Empack no maneja postal code.
        //$address->setRegionId($data['address']['region_id']); Opcional.
        //$address->setIsDefaultShipping(1); Opcional.
        //$address->setIsDefaultBilling(1); Opcional.
        $address->setCustomerId($customer->getId());
        $this->_addressRepository->save($address); */

    }

    /**
     * Generates encrypted random password.
     * @return string
     * @throws LocalizedException
     */
    private function randomPassword()
    {
        $chars = Random::CHARS_LOWERS
            . Random::CHARS_UPPERS
            . Random::CHARS_DIGITS;

        return $this->_mathRandom->getRandomString(15, $chars);
    }

    /**
     * Returns status formatted.
     * @param $frozenFor
     * @return
     */
    private function getStatus($frozenFor)
    {
        return ($frozenFor == 'N') ? 'approved' : 'pending_approval';
    }

    /**
     *
     * @param $email
     * @return false|string
     */
    private function getEmailFormatted($email)
    {
        $formattedEmail = $email;
        if (strpos($email, ';') !== false) {
            $formattedEmail = substr($email, 0, strpos($email, ";"));
        } elseif (strpos($email, ',') !== false) {
            $formattedEmail = substr($email, 0, strpos($email, ","));
        }

        return $formattedEmail;
    }

    /**
     * @param $customerObject object
     * @param $adminCustomer CustomerInterface
     * @return CompanyInterface|bool
     */
    private function createCompany(object $customerObject, CustomerInterface $adminCustomer)
    {
        $company = $this->companyInterfaceFactory->create();
        $company->setRootGroupId(1);
        $company->setStatus($customerObject->status);
        $company->setName($customerObject->first_name);
        $company->setLegalName($customerObject->legal_name);
        $company->setCustomerGroupId($customerObject->list_num);
        $company->setEmail($customerObject->email);
        $company->setTaxId(null);
        $company->setReSellerId(null);
        $company->setStreet($customerObject->street);
        $company->setCity($customerObject->city);
        $company->setCountryId("EC");
        $company->setRegion($customerObject->region);
        $company->setRegionId($customerObject->region_id);
        $company->setPostcode($customerObject->postcode);
        $company->setTelephone($customerObject->phone);
        $company->setIsAllowedToQuote(1);
        $company->setAllowedPaymentMethods([]);
        try {
            return $this->companyManagement->createCompany($company, $adminCustomer);
        } catch (\Exception $e) {
            $this->logger->error("An error has occurred creating company: " . $e->getMessage());
            $this->resTable['fail']++;
        }
        return false;
    }

    private function getRegion($state, $city)
    {
        if (!is_null($state)) {
            $region = $this->regionFactory->loadByCode($state, 'EC');
        } else {
            $region = $this->helperSAP->getRegionByCity($city);
            $region = $this->regionFactory->load($region);
        }
        return $region;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function getCustomerByAttribute($attribute)
    {
        $filter1 = $this->_filterBuilder
            ->setField("sap_customer_id")
            ->setValue($attribute)
            ->setConditionType("eq")->create();

        $filterGroup1 = $this->_filterGroupBuilder
            ->addFilter($filter1)->create();

        $searchCriteria = $this->_searchCriteriaBuilder
            ->setFilterGroups([$filterGroup1])
            ->create();
        $customer = $this->_customerRepository->getList($searchCriteria)->getItems();

        if (!$customer) {
            throw new NoSuchEntityException(__('No existe el customer.'));
        }

        return array_first($customer);
    }
}
