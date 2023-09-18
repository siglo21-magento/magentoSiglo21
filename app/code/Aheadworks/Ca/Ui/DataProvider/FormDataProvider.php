<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider;

use Magento\Framework\ObjectManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class FormDataProvider
 * @package Aheadworks\Ca\Ui\DataProvider
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_ca_persistor';

    /**
     * @var AbstractCollection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PoolInterface
     */
    private $modifiersPool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ObjectManagerInterface $objectManager
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $modifiersPool
     * @param string|null $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ObjectManagerInterface $objectManager,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        PoolInterface $modifiersPool,
        $collectionFactory = null,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $collectionFactory = $objectManager->get($collectionFactory);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->modifiersPool = $modifiersPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $preparedData = [];
        $idFieldName = $this->collection->getIdFieldName();
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);
        if (!empty($dataFromForm) && (is_array($dataFromForm))) {
            $id = $dataFromForm['company']['id'] ?? null;
            $data = $dataFromForm;
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $entities = $this->getCollection()->addFieldToFilter($idFieldName, $id)->getItems();

            foreach ($entities as $entity) {
                if ($id == $entity->getId()) {
                    $data = $entity->getData();
                }
            }
        }

        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
             $data = $modifier->modifyData($data);
        }
        if (!empty($data)) {
            $preparedData[$id] = $data;
        }

        return $preparedData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        return $this->modifyMeta($meta);
    }

    /**
     * Modify meta
     *
     * @param array $meta
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyMeta($meta)
    {
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
