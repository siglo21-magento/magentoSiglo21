<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model\Sync;

use Aventi\PriceList\Api\Data\PriceListInterfaceFactory;
use Aventi\PriceList\Api\PriceListRepositoryInterface;
use Aventi\SAP\Helper\Attribute;
use Aventi\SAP\Helper\Data;
use Aventi\SAP\Helper\SAP;
use Aventi\SAP\Logger\Logger;
use Aventi\SAP\Model\Check;
use Aventi\SAP\Model\Integration;
use Bcn\Component\Json\Exception\ReadingError;
use Bcn\Component\Json\Reader;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Group;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class Price extends Integration
{
    const TYPE_URI = 'price';

    private $resTable = [
        'check' => 0,
        'fail' => 0,
        'not_found' => 0,
        'updated' => 0,
        'new' => 0
    ];

    /**
     * @var Data
     */
    private $_data;

    /**
     * @var Check
     */
    private $_check;

    /**
     * @var ProductRepositoryInterface
     */
    private $_productRepository;

    /**
     * @var \Aventi\PriceList\Api\PriceListRepositoryInterface
     */
    private $_priceListRepositoryInterface;

    /**
     * @var \Aventi\PriceList\Api\Data\PriceListInterfaceFactory
     */
    private $_priceListInterfaceFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $_filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $_filterGroupBuilder;

    /**
     * @var \Magento\Customer\Api\Data\GroupInterfaceFactory
     */
    private $_groupFactory;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $_groupRepositoryInterface;

    /**
     * @var \Magento\Customer\Model\Group
     */
    private $_group;

    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $_helperSAP;

    /**
     * @param Attribute $attribute
     * @param Logger $logger
     * @param DriverInterface $driver
     * @param Filesystem $filesystem
     * @param ProductRepositoryInterface $productRepository
     * @param Data $data
     * @param Check $check
     * @param PriceListRepositoryInterface $priceListRepositoryInterface
     * @param PriceListInterfaceFactory $priceListInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param GroupInterfaceFactory $groupFactory
     * @param GroupRepositoryInterface $groupRepositoryInterface
     * @param Group $group
     * @param SAP $helperSAP
     */
    public function __construct(
        Attribute $attribute,
        Logger $logger,
        DriverInterface $driver,
        Filesystem $filesystem,
        ProductRepositoryInterface $productRepository,
        Data $data,
        Check $check,
        \Aventi\PriceList\Api\PriceListRepositoryInterface $priceListRepositoryInterface,
        \Aventi\PriceList\Api\Data\PriceListInterfaceFactory $priceListInterfaceFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Customer\Api\Data\GroupInterfaceFactory $groupFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepositoryInterface,
        \Magento\Customer\Model\Group $group,
        \Aventi\SAP\Helper\SAP $helperSAP
    ) {
        parent::__construct($attribute, $logger, $driver, $filesystem);
        $this->_productRepository = $productRepository;
        $this->_data = $data;
        $this->_check = $check;
        $this->_priceListRepositoryInterface = $priceListRepositoryInterface;
        $this->_priceListInterfaceFactory = $priceListInterfaceFactory;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->_filterGroupBuilder = $filterGroupBuilder;
        $this->_groupFactory = $groupFactory;
        $this->_groupRepositoryInterface = $groupRepositoryInterface;
        $this->_group = $group;
        $this->_helperSAP = $helperSAP;
    }

    /**
     * @param boolean $fast
     * @throws \Bcn\Component\Json\Exception\ReadingError
     * @throws FileSystemException
     */
    public function updatePrice(bool $fast)
    {
        $start = 0;
        $rows = 1000;
        $flag = true;

        $progressBar = null;
        while ($flag) {
            $jsonData = $this->_data->getResource(self::TYPE_URI, $start, $rows, $fast);
            $jsonPath = $this->getJsonPath($jsonData, self::TYPE_URI);
            if ($jsonPath) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(null,Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $products = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($products as $product) {
                    $dataProduct = (object) [
                        'sku' => $product['ItemCode'],
                        'price' => $product['Price'],
                        'list_price' =>  $product['PriceList']
                    ];
                    $this->managerPrice($dataProduct);
                    $this->advanceProgressBar($progressBar);
                    // Debug only.
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
     * @return void
     * @throws FileSystemException
     * @throws ReadingError
     */
    public function updatePriceList()
    {
        $start = 0;
        $rows = 1000;
        $flag = true;

        while ($flag) {
            $jsonData =  $this->_data->getResource('price_list', $start, $rows, false);
            $jsonPath = $this->getJsonPath($jsonData, 'price_list');
            if ($jsonPath) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter('',Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $priceLists = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($priceLists as $priceList) {
                    $fullName = $priceList['ListNum'] . ' - ' . $priceList['ListName'];
                    $this->managerPriceList($fullName);
                    $this->advanceProgressBar($progressBar);
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

    public function managerPriceList($fullName)
    {
        try {
            $groupId = $this->_helperSAP->getGroupId($fullName);
            $group = $this->_groupRepositoryInterface->getById($groupId);
            $this->resTable['check']++;
        } catch (LocalizedException $e) {
            $group = $this->_groupFactory->create();
            $group->setCode($fullName);
            $group->setTaxClassId(3);
            $this->_groupRepositoryInterface->save($group);
            $this->resTable['updated']++;
        }
    }

    /**
     * @param $sku
     * @param $list
     * @return string|null
     */
    public function getPriceBySkuAndList($sku, $list): ?string
    {
        $filter1 = $this->_filterBuilder
            ->setField("sku")
            ->setValue($sku)
            ->setConditionType("eq")->create();

        $filterGroup1 = $this->_filterGroupBuilder
            ->addFilter($filter1)->create();

        $filter2 = $this->_filterBuilder
            ->setField("group")
            ->setValue($list)
            ->setConditionType("eq")->create();

        $filterGroup2 = $this->_filterGroupBuilder
            ->addFilter($filter2)->create();

        $searchCriteria = $this->_searchCriteriaBuilder
            ->setFilterGroups([$filterGroup1, $filterGroup2])
            ->create();
        $id = null;
        try {
            $items = $this->_priceListRepositoryInterface->getList($searchCriteria)->getItems();
            foreach ($items as $item) {
                $id = $item->getPricelistId();
            }
        } catch (LocalizedException $e) {
            $this->logger->error("ERROR get list price group: " . $e->getMessage());
        }

        return $id;
    }

    /**
     * Save the price list.
     *
     * @method _savePriceList
     * @param object $data
     * @param $priceList
     * @throws LocalizedException
     */
    public function _savePriceList(object $data , $priceList)
    {
        $priceList->setSku($data->sku);
        $priceList->setGroup($data->list_price);
        $priceList->setPrice($data->price);
        $this->_priceListRepositoryInterface->save($priceList);
    }

    /**
     * Save the data from the $data in the instance source item $sourceItem.
     * @param object $data With the following attributes: sku, price, list
     *
     */
    public function managerPrice(object $data)
    {
        try{
            $item = $this->_productRepository->get($data->sku);
            $id = $this->getPriceBySkuAndList($data->sku, $data->list_price);
            $priceList = $this->_priceListInterfaceFactory->create();
            if ($id){
                $priceList = $this->_priceListRepositoryInterface->get($id);
            }
            $resultCheckList = $this->_check->checkData($data, $priceList, 'price');

            if (!$resultCheckList) {
                $this->resTable['check']++;
            } else {
                $this->saveFields($item, $resultCheckList);
                $this->_savePriceList($data , $priceList);
                $this->resTable['updated']++;
            }

        } catch (NoSuchEntityException $e) {
            $this->resTable['not_found']++;
        } catch (LocalizedException $e) {
            $this->resTable['fail']++;
            $this->logger->error("An error has occurred creating price: " . $e->getMessage());
        }
    }
}
