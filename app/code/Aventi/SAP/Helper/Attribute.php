<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aventi\SAP\Helper;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\Source\TableFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class Attribute extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $attributeValues;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
     */
    protected $tableFactory;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory
     */
    protected $optionFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    private $groupCollection;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    private $_attribute;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $catergoryCollection;


    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param TableFactory $tableFactory
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeOptionLabelInterfaceFactory $optionLabelFactory
     * @param AttributeOptionInterfaceFactory $optionFactory
     * @param Collection $groupCollection
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $attribute
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $groupCollection,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $attribute,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        parent::__construct($context);
        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->groupCollection = $groupCollection;
        $this->_attribute = $attribute;
        $this->catergoryCollection = $categoryCollectionFactory;
    }

    /**
     * Get attribute by code.
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode);
    }

    /**
     * Find or create a matching attribute option
     *
     * @param string $attributeCode Attribute the option should exist in
     * @param string $label Label to find or add
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createOrGetId($attributeCode, $label)
    {
        if (strlen($label) < 1) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Label for %1 must not be empty.', $attributeCode)
            );
        }

        // Does it already exist?
        $optionId = $this->getOptionId($attributeCode, $label);

        if (!$optionId) {
            // If no, add it.

            /** @var \Magento\Eav\Model\Entity\Attribute\OptionLabel $optionLabel */
            $optionLabel = $this->optionLabelFactory->create();
            $optionLabel->setStoreId(0);
            $optionLabel->setLabel($label);

            $option = $this->optionFactory->create();
            $option->setLabel($label);
            $option->setStoreLabels([$optionLabel]);
            $option->setSortOrder(0);
            $option->setIsDefault(false);

            $this->attributeOptionManagement->add(
                \Magento\Catalog\Model\Product::ENTITY,
                $this->getAttribute($attributeCode)->getAttributeId(),
                $option
            );

            // Get the inserted ID. Should be returned from the installer, but it isn't.
            $optionId = $this->getOptionId($attributeCode, $label, true);
        }

        return $optionId;
    }

    /**
     * Find the ID of an option matching $label, if any.
     *
     * @param string $attributeCode Attribute code
     * @param string $label Label to find
     * @param bool $force If true, will fetch the options even if they're already cached.
     * @return int|false
     */
    public function getOptionId($attributeCode, $label, $force = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];

            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }

    public function resolveGroup($list)
    {
        $customerGroups = $this->groupCollection->toOptionArray();
        $groupId = null;
        foreach ($customerGroups as $customerGroup) {
            $formatLabel = $this->formatGroupLabel($customerGroup['label']);
            if ($list == $formatLabel) {
                $groupId = $customerGroup['value'];
            }
        }
        return $groupId;
    }

    public function formatGroupLabel($label)
    {
        $format = explode("-", $label);
        if (is_array($format)) {
            $format = trim($format[0]);
        }
        return $format;
    }

    /**
     * @param $category
     * @return array
     */
    public function getCategoryIds($category): array
    {
        $sapAttributeId = $this->_attribute->getIdByCode('catalog_category', 'sap');

        $subQuery1 = new \Zend_Db_Expr(sprintf("select DISTINCT ccev.entity_id
						from catalog_category_entity_varchar ccev
						where ccev.value = '%s' and ccev.attribute_id = %s", $category['U_Clase'], $sapAttributeId));
        $subQuery2 = new \Zend_Db_Expr(sprintf("select DISTINCT ccev.entity_id
						from catalog_category_entity_varchar ccev
						where ccev.value = '%s' and ccev.attribute_id = %s", $category['U_Tipo'], $sapAttributeId));
        $subQuery3 = new \Zend_Db_Expr(sprintf("select DISTINCT ccev.entity_id
						from catalog_category_entity_varchar ccev
						where ccev.value = '%s' and ccev.attribute_id = %s", $category['U_GrupoWeb'], $sapAttributeId));

        $sapCategoryIds = $this->catergoryCollection->create();

        $sapCategoryIds->getSelect()
            ->join(['fa' => 'catalog_category_entity'], 'fa.parent_id = e.entity_id')
            ->join(['gran' => 'catalog_category_entity'], 'gran.parent_id = fa.entity_id')
            ->where("gran.entity_id IN (". $subQuery1 .")")
            ->where("fa.entity_id IN (". $subQuery2 .")")
            ->where("e.entity_id IN (". $subQuery3 .")");

        $sapCategoryIds->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['son' => 'e.entity_id', 'sub' => 'fa.entity_id', 'fam' => 'gran.entity_id']);

        $data = $sapCategoryIds->getFirstItem();
        $treeIds = array_values($data->getData());

        return $treeIds ?: [];
    }
}
