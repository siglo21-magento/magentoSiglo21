<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel as MagentoFrameworkAbstractModel;

/**
 * Class AbstractResourceModel
 * @package Aheadworks\Ctq\Model\ResourceModel
 */
abstract class AbstractResourceModel extends AbstractDb
{
    /**
     * @var array
     */
    protected $entityArguments = [];

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Save an object
     *
     * @param MagentoFrameworkAbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(MagentoFrameworkAbstractModel $object)
    {
        $object->validateBeforeSave();
        $object->beforeSave();
        $this->entityManager->save($object);
        $object->afterSave();
        $object->setOrigData();
        return $this;
    }

    /**
     * Load an object
     *
     * @param MagentoFrameworkAbstractModel $object
     * @param int $objectId
     * @param string $field
     * @return $this
     */
    public function load(MagentoFrameworkAbstractModel $object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $arguments = $this->getArgumentsForEntity();
            $this->entityManager->load($object, $objectId, $arguments);
            $object->afterLoad();
            $object->setOrigData();
        }
        return $this;
    }

    /**
     * Delete an object
     *
     * @param MagentoFrameworkAbstractModel $object
     * @return $this
     */
    public function delete(MagentoFrameworkAbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Retrieve arguments array for entity
     *
     * @return array
     */
    protected function getArgumentsForEntity()
    {
        return $this->entityArguments;
    }

    /**
     * Set arguments array for entity
     *
     * @param string $key
     * @param mixed $value
     */
    public function setArgumentsForEntity($key, $value)
    {
        $this->entityArguments[$key] = $value;
    }
}
