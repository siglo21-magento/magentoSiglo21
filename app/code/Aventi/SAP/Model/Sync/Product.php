<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model\Sync;

use Aventi\SAP\Helper\Attribute;
use Aventi\SAP\Helper\Data;
use Aventi\SAP\Helper\SAP;
use Aventi\SAP\Logger\Logger;
use Aventi\SAP\Model\Check;
use Aventi\SAP\Model\Integration;
use Bcn\Component\Json\Reader;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

class Product extends Integration
{
    const TYPE_URI = 'product';

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
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    private $productTaxClassSource;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $product;

    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $helperSAP;

    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    private $categoryLinkManagement;

    /**
     * @var Check
     */
    private $_check;

    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection
     */
    private $_urlRewriteCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $store;

    /**
     * Product constructor.
     * @param Attribute $attribute
     * @param Logger $logger
     * @param DriverInterface $driver
     * @param Filesystem $filesystem
     * @param Data $data
     * @param \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactory $product
     * @param SAP $helperSAP
     * @param CategoryLinkManagementInterface $categoryLinkManagementInterface
     * @param Check $check
     * @param UrlRewriteCollectionFactory $urlRewriteCollection
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Aventi\SAP\Helper\Attribute $attribute,
        \Aventi\SAP\Logger\Logger $logger,
        \Magento\Framework\Filesystem\DriverInterface $driver,
        \Magento\Framework\Filesystem $filesystem,
        \Aventi\SAP\Helper\Data $data,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $product,
        \Aventi\SAP\Helper\SAP $helperSAP,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface,
        \Aventi\SAP\Model\Check $check,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($attribute, $logger, $driver, $filesystem);
        $this->data = $data;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->productRepository = $productRepository;
        $this->product = $product;
        $this->helperSAP = $helperSAP;
        $this->categoryLinkManagement = $categoryLinkManagementInterface;
        $this->_check = $check;
        $this->_urlRewriteCollection = $urlRewriteCollection;
        $this->store = $storeManager;
    }


    /**
     * @param object $data
     * @return void
     * @throws NoSuchEntityException
     */
    public function managerProduct(object $data)
    {
        try {
            $item = $this->productRepository->get($data->sku);
            $checkProduct = $this->_check->checkData($data, $item, 'product');
            $checkCategories = $this->_check->checkCategories($data, $item);

            if (!$checkProduct && !$checkCategories) {
                $this->resTable['check']++;
            } else {
                if ($checkProduct) {
                    $this->saveFields($item, $checkProduct);
                }
                if ($checkCategories && $data->category_id !== null) {
                    $this->categoryLinkManagement->assignProductToCategories($data->sku, $data->category_id);
                }
                $this->resTable['updated']++;
            }
            if ($data->list_material) {
                $this->manageListMaterialImage($data->sku, $data->list_items);
            }

        } catch (NoSuchEntityException $e) { // Product no found
            $this->createProduct($data);
            if ($data->list_material) {
                $this->manageListMaterialImage($data->sku, $data->list_items);
            }
        } catch (LocalizedException $e) {
            $this->resTable['fail']++;
            $this->logger->error("An error has occurred: " . $e->getMessage());
        }
    }

    /**
     * @param string $sku
     * @param array $items
     * @return void
     * @throws NoSuchEntityException
     */
    public function manageListMaterialImage(string $sku, array $items)
    {
        try {
            $materialProduct = $this->productRepository->get($sku);
            $imagesList = $materialProduct->getMediaGalleryEntries();
            if (!count($imagesList)) {
                $singleProduct = $this->productRepository->get($items[array_key_first($items)]);
                $images = $singleProduct->getMediaGalleryImages();
                foreach ($images as $image) {
                    // Check if the file path exists
                    if ($path = $image->getPath()) {
                        $attributes = null;
                        if($singleProduct->getImage() == $image->getFile()){
                            $attributes = ['image', 'thumbnail', 'small_image'];
                        }

                        $materialProduct->addImageToMediaGallery(
                            $path,
                            $attributes,
                            false,
                            false
                        );
                        $materialProduct->save();
                    }
                }
            }
        } catch (NoSuchEntityException $e) { // Product no found
            $this->logger->error("An error NoSuchEntityException has occurred: " . $e->getMessage());
        } catch (LocalizedException $e) {
            $this->logger->error("An error has occurred: " . $e->getMessage());
        }
    }

    /**
     * Get the tax_id
     *
     * @param $value
     * @return int|mixed
     */
    public function getTaxId($value)
    {
        $taxClassess = $this->productTaxClassSource->getAllOptions();

        foreach ($taxClassess as $tax) {
            if ($tax['label'] == $value) {
                return $tax['value'];
            }
        }
        return 0;
    }

    /**
     * Generate pretty url by products
     *
     * @param $name
     * @return string
     * @throws NoSuchEntityException
     */
    public function generateURL($name): string
    {
        $url = preg_replace('#[^0-9a-z]+#i', '-', $name);
        $url = strtolower($url);

        $urlCollection = $this->_urlRewriteCollection->create();
        $urlCollectionData = $urlCollection->addFieldToFilter('request_path', ['eq' => $url . '.html'])
            ->addFieldToFilter('store_id', ['eq' => $this->store->getStore()->getId()])
            ->getFirstItem();

        if ($urlCollectionData->getData('entity_id')) {
            return $this->generateNewUrl($url);
        }
        return $url;
    }

    /**
     * Update or Create the products
     *
     * @throws \Exception
     */
    public function updateProduct(bool $fast)
    {
        $start = 0;
        $rows = 1000;
        $flag = true;

        while ($flag) {
            $jsonData = $this->data->getResource(self::TYPE_URI, $start, $rows, $fast);
            $jsonPath = $this->getJsonPath($jsonData, self::TYPE_URI);
            if ($jsonPath) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter(null, Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $products = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($products as $product) {
                    $status = $product['frozenFor'] ?? '';

                    if ($status == 'N') {
                        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
                    } else {
                        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
                    }

                    $listMaterials = $product['ListaMateriales'] ?? 0;
                    $listItems = $product['list_items'] ?? [];
                    $parent = $product['U_GrupoWeb'] ?? '';
                    $subparent = $product['U_Tipo'] ?? '';
                    $child = $product['U_Clase'] ?? '';
                    if ($listMaterials) {
                        $longDescription ='';
                        try {
                            foreach ($listItems as $item) {
                                $itemMaterial = $this->productRepository->get($item);
                                $longDescription .= !is_null($itemMaterial->getDescription()) ?
                                    preg_replace(
                                        '/(["])\\1+/',
                                        '$1',
                                        rtrim(html_entity_decode($itemMaterial->getDescription()), '"')
                                    ) : '';
                            }
                        } catch (NoSuchEntityException $e) {
                            continue;
                        }
                    } else {
                        $longDescription = !is_null($product['UserText']) ?
                            preg_replace(
                                '/(["])\\1+/',
                                '$1',
                                rtrim(html_entity_decode($product['UserText']), '"')
                            ) : '';
                    }
                    $itemObject = (object)[
                        'sku' =>  $product['ItemCode'],
                        'name' => $product['ItemName'] ?? '',
                        'brand' => isset($product['Marca']) ? $this->getOptionId($product['Marca'], 'mgs_brand') : 0,
                        'tax_class_id' => isset($product['TaxCodeAR']) ? $this->getTaxId($product['TaxCodeAR']) : '',
                        'status' =>  $status,
                        'state_slow' => ($product['U_Exx_Des_EstadoLento'] == 'S') ? 1 : 0,
                        'ref' => $product['SuppCatNum'],
                        'upc' => $product['CodeBars'],
                        'web_articule' => ($product['U_ArticuloWeb'] == 'Y' || $product['U_ArticuloWeb'] == 'S') ? 1 : 0,
                        'bodega_lm' => $product['BodegaLM'],
                        'list_material' => $listMaterials,
                        'list_items' => $listItems,
                        'u_marca' => $product['U_Marca'],
                        'type' => isset($product['Tipo']) ? $this->getOptionId($product['Tipo'], 'type') : 0,
                        'class' => isset($product['Clase']) ? $this->getOptionId($product['Clase'], 'class') : 0,
                        'parent' => $parent,
                        'subparent' => $subparent,
                        'child' => $child,
                        'category_id' => $this->attributeHelper->getCategoryIds($product),
                        'store_id' => 0,
                        'long_description' => $longDescription,
                        'weight' => $product['SWeight1'] ?? 0
                    ];
                    $this->managerProduct($itemObject);
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
     * @param object $data
     * @return void
     * @throws NoSuchEntityException
     */
    private function createProduct(object $data)
    {
        $item = $this->product->create();
        $item->setSku($data->sku);
        //$item->setStoreId(0);
        $item->setVisibility(4);
        $item->setName($data->name);
        $item->setPrice(0);
        $item->setQty(0);
        $item->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $item->setAttributeSetId(4); // 4 -> Default.
        $item->setStatus($data->status);
        $item->setVisibility(4); // 4 -> Catalog and Search.
        $item->setDescription($data->long_description);
        $item->setCustomAttributes([
            'tax_class_id' => $data->tax_class_id,
            'state_slow' => $data->state_slow,
            'ref' => $data->ref,
            'upc'=> $data->upc,
            'web_articule' => $data->web_articule,
            'bodega_lm' => $data->bodega_lm,
            'list_material' => $data->list_material,
            'u_marca' => $data->u_marca,
        ]);
        $item->setData('mgs_brand', $data->brand);
        $item->setData('type', $data->type);
        $item->setData('class', $data->class);
        $item->setWeight($data->weight);
        $item->setUrlKey($this->generateURL($data->name));

        try {
            $this->productRepository->save($item);
            if ($data->category_id != null) {
                $this->categoryLinkManagement->assignProductToCategories($data->sku, $data->category_id);
            }
            $this->resTable['new']++;
        } catch (CouldNotSaveException | InputException | StateException $e) {
            $this->logger->error("An error has occurred creating product: " . $e->getMessage());
        }
    }

    /**
     * Generates new url if it exists.
     * @param string $url
     * @return string
     */
    private function generateNewUrl(string $url): string
    {
        $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
        return $url . '-' . $randomString;
    }
}
