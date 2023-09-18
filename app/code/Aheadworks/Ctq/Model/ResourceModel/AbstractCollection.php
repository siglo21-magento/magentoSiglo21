<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\ResourceModel;

use Aheadworks\Ctq\Api\Data\OwnerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as FrameworkAbstractCollection;

/**
 * Class AbstractCollection
 * @package Aheadworks\Ctq\Model\ResourceModel
 */
class AbstractCollection extends FrameworkAbstractCollection
{
    /**
     * @var string[]
     */
    private $linkageTableNames = [];

    /**
     * Attach relation table data to collection items
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @param string $fieldName
     * @return void
     */
    public function attachRelationTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from([$tableName . '_table' => $this->getTable($tableName)])
                ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);
            $result = $connection->fetchAll($select);

            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $resultIds = [];
                $id = $item->getData($columnName);
                foreach ($result as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        if (is_array($columnNameRelationTable)) {
                            $fieldValue = [];
                            foreach ($columnNameRelationTable as $columnNameRelation) {
                                $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                            }
                            $resultIds[] = $fieldValue;
                        } else {
                            $resultIds[] = $data[$columnNameRelationTable];
                        }
                    }
                }
                $item->setData($fieldName, $resultIds);
            }
        }
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @param string $fieldName
     * @return $this
     */
    public function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName
    ) {
        if ($this->getFilter($columnFilter)) {
            $linkageTableName = $columnFilter . '_table';
            if (in_array($linkageTableName, $this->linkageTableNames)) {
                $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
                return $this;
            }

            $this->linkageTableNames[] = $linkageTableName;
            $select = $this->getSelect();
            $select->joinLeft(
                [$linkageTableName => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );

            $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
        }

        return $this;
    }

    /**
     * Attach owner name
     *
     * @param string $tableName
     * @param string $linkageColumnName
     * @param int $ownerType
     * @return $this
     */
    protected function attachOwnerName($tableName, $linkageColumnName, $ownerType)
    {
        $ids = $this->getOwnerIds($ownerType);
        if (count($ids)) {
            $dataFromTable = $this->getDataFromTable($tableName, $linkageColumnName, $ids);
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $id = $item->getData(OwnerInterface::OWNER_ID);
                $itemOwnerType = $item->getData(OwnerInterface::OWNER_TYPE);
                foreach ($dataFromTable as $data) {
                    if ($data[$linkageColumnName] == $id && $ownerType == $itemOwnerType) {
                        $ownerName = $data['firstname'] . ' ' . $data['lastname'];
                        $item->setData(OwnerInterface::OWNER_NAME, $ownerName);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve owner ids by type
     *
     * @param int $ownerType
     * @return array
     */
    protected function getOwnerIds($ownerType)
    {
        $this->load();
        $col = [];
        foreach ($this->getItems() as $item) {
            if ($item->getData(OwnerInterface::OWNER_TYPE) == $ownerType) {
                $col[] = $item->getData(OwnerInterface::OWNER_ID);
            }
        }

        return $col;
    }

    /**
     * Retrieve data from table
     *
     * @param string $tableName
     * @param string $linkageColumnName
     * @param array $ids
     * @return array
     */
    protected function getDataFromTable($tableName, $linkageColumnName, $ids)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from([$tableName . '_table' => $this->getTable($tableName)])
            ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);

        return $connection->fetchAll($select);
    }
}
