<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

/**
 * Class AbstractResourceModel
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
abstract class AbstractResourceModel extends AbstractDb
{
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
     * Save object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $object->validateBeforeSave();
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Load object
     *
     * @param AbstractModel $object
     * @param int $objectId
     * @param string $field
     * @return $this
     */
    public function load(AbstractModel $object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $this->entityManager->load($object, $objectId, []);
        }
        return $this;
    }

    /**
     * Delete object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
