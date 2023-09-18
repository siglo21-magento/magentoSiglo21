<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aventi\SAP\Model;

use Aventi\SAP\Logger\Logger;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Exception\NoSuchEntityException;
use UnexpectedValueException;

class Check
{
    /**
     * @var Configurable
     */
    private $_configurableProduct;

    /**
     * @var Logger
     */
    private $_logger;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $_productRepository;

    /**
     * Check constructor.
     * @param Configurable $configurableProduct
     * @param Logger $logger
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProduct,
        \Aventi\SAP\Logger\Logger $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->_configurableProduct = $configurableProduct;
        $this->_logger = $logger;
        $this->_productRepository = $productRepository;
    }

    /**
     * Check the data for differences.
     *
     * @param object $data The new data to compare
     * @param object $item The original data from DB
     * @param string $option The type of data to be checked. There are 3 types:
     * product, stock and price.
     * @return bool|array returns <b>FALSE</b> if there aren't
     * differences, otherwise returns the array containing the data.
     * @throws UnexpectedValueException
     */
    public function checkData(object $data, object $item, string $option)
    {
        $currentData = null;
        $headData = null;
        switch ($option) {
            case 'product':
                $currentData = [
                    'name' => $data->name,
                    'brand' => $data->brand,
                    'type' => $data->type,
                    'class' => $data->class,
                    'tax_class_id' => $data->tax_class_id,
                    'status' => $data->status,
                    'state_slow' => $data->state_slow,
                    'ref' => $data->ref,
                    'upc' => $data->upc,
                    'web_articule' => $data->web_articule,
                    'bodega_lm' => $data->bodega_lm,
                    'list_material' => $data->list_material,
                    'u_marca' => $data->u_marca,
                    'description' => $data->long_description,
                    'weight' => (double) $data->weight
                ];
                $headData = [
                    'name' => $item->getData('name'),
                    'brand' => $item->getData('mgs_brand'),
                    'type' => $item->getData('type'),
                    'class' => $item->getData('class'),
                    'tax_class_id' => $item->getData('tax_class_id'),
                    'status' => (int) $item->getData('status'),
                    'state_slow' => (int) $item->getData('state_slow'),
                    'ref' =>  $item->getData('ref'),
                    'upc' =>  $item->getData('upc'),
                    'web_articule' => (int) $item->getData('web_articule'),
                    'bodega_lm' =>  $item->getData('bodega_lm'),
                    'list_material' => (int) $item->getData('list_material'),
                    'u_marca' => $item->getData('u_marca'),
                    'description' => $item->getDescription(),
                    'weight' => (double) $item->getWeight()
                ];
                break;
            case 'price':
                $currentData = [
                    'price' => $this->formatDecimalNumber($data->price)
                ];
                $headData = [
                    'price' => $item->getPrice()
                ];
                break;
            case 'stock':
                $currentData = [
                    'qty' => $data->qty
                ];
                $headData = [
                    'qty' => $item->getQuantity()
                ];
                break;
            case 'customer':
                $currentData = [
                    'email' => $data->email,
                    'firstname' => $data->first_name,
                    'lastname' => $data->last_name,
                    'group_id' => $data->list_num,
                    'sap_customer_id' => $data->card_code,
                    'slp_code' => $data->slp_code,
                    'identification_customer' => $data->ruc,
                    'owner_code' => $data->employee_id,
                    'user_code' => $data->user_code,
                    'seller_email' => $data->seller_email
                ];
                $headData = [
                    'email' => $item->getEmail(),
                    'firstname' => $item->getFirstname(),
                    'lastname' => $item->getLastname(),
                    'group_id' => $item->getGroupId(),
                    'sap_customer_id' => $item->getCustomAttribute('sap_customer_id') ? $item->getCustomAttribute('sap_customer_id')->getValue() : "",
                    'slp_code' => $item->getCustomAttribute('slp_code') ? $item->getCustomAttribute('slp_code')->getValue() : "",
                    'identification_customer' => $item->getCustomAttribute('identification_customer') ? $item->getCustomAttribute('identification_customer')->getValue() : "",
                    'owner_code' => $item->getCustomAttribute('owner_code') ? $item->getCustomAttribute('owner_code')->getValue() : "",
                    'user_code' => $item->getCustomAttribute('user_code') ? $item->getCustomAttribute('user_code')->getValue() : "",
                    'seller_email' => $item->getCustomAttribute('seller_email') ? $item->getCustomAttribute('seller_email')->getValue() : "",
                ];
                break;
            case 'company':
                $currentData = [
                    'status' => $data->status,
                    'name' => $data->first_name,
                    'customer_group_id' => $data->list_num,
                    'legal_name' => $data->first_name,
                    'email' => $data->email,
                    'street' => $data->street,
                    'city' => $data->city,
                    'region' => $data->region,
                    'region_id' => $data->region_id,
                    'postcode' => $data->postcode,
                    'telephone' => $data->phone
                ];
                $headData = [
                    'status' => $item->getStatus(),
                    'name' => $item->getName(),
                    'customer_group_id' => $item->getCustomerGroupId(),
                    'legal_name' => $item->getLegalName(),
                    'email' => $item->getEmail(),
                    'street' => $item->getStreet(),
                    'city' => $item->getCity(),
                    'region' => $item->getRegion(),
                    'region_id' => $item->getRegionId(),
                    'postcode' => $item->getPostcode(),
                    'telephone' => $item->getTelephone()
                ];
                break;
            default:
                throw new UnexpectedValueException('Unexpected value');
        }
        $checkData = array_diff_assoc($currentData, $headData);

        if (empty($checkData)) {
            return false;
        }
        return $checkData;
    }

    /**
     * Checks if the product is a child's configurable product.
     * @param ProductInterface $item
     * @return bool Returns <b>TRUE</b> if the product is a child; otherwise
     * returns <b>FALSE</b>.
     */
    public function checkConfigurable(ProductInterface $item): bool
    {
        $product = $this->_configurableProduct->getParentIdsByChild($item->getId());
        return $product ? true : false;
    }

    /**
     * Checks categories product and retrieves differences.
     * @param object $data
     * @param ProductInterface $product
     * @return array|false returns <b>FALSE</b> if there aren't
     * differences, otherwise returns an array containing the data.
     */
    public function checkCategories(object $data, ProductInterface $product)
    {
        $arrayValues = [];
        if (is_array($data->category_id)) {
            $arrayValues = array_values($data->category_id);
        }

        if ($product->getCategoryIds() === null || $product->getCategoryIds() === []) {
            $categoryDiff = array_diff($arrayValues, $product->getCategoryIds());
        } else {
            $categoryDiff = array_diff($product->getCategoryIds(), $arrayValues);
        }

        if (empty($categoryDiff)) {
            return false;
        }
        return $categoryDiff;
    }

    /**
     * Checks categories configurable product and retrieves differences.
     * @param object $data
     * @return array|false returns <b>FALSE</b> if there aren't
     * differences, otherwise returns an array containing the data.
     */
    public function checkCategoryConfigurable(object $data)
    {
        $skuConfigurable = (string) substr($data->sku, 0, 8);
        $arrayValues = [];
        try {
            $confProduct = $this->_productRepository->get($skuConfigurable, false, 0);
            if (is_array($data->category_id)) {
                $arrayValues = array_values($data->category_id);
            }
            $categoryDiff = array_diff($arrayValues, $confProduct->getCategoryIds());
            if (empty($categoryDiff)) {
                return false;
            }
            $categoryDiff[] = $skuConfigurable;
            return $categoryDiff;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Format a decimal number depending its separator.
     * @param $number
     * @return string
     */
    public function formatDecimalNumber($number): string
    {
        return number_format($number, 6, '.', '');
    }
}
