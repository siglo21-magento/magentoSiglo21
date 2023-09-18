<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Payment\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\RequestInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\AsyncUpdater\CreditLimit\Scheduler;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class CustomerGroupConfig
 *
 * @package Aheadworks\CreditLimit\Model\Payment\Config\Backend
 */
class CustomerGroupConfig extends ArraySerialized
{
    /**
     * Path to field in config
     */
    const CONFIG_PATH = 'groups/aw_credit_limit/fields/credit_limit_per_group_config';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Scheduler
     */
    private $updateScheduler;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param JsonSerializer $serializer
     * @param RequestInterface $request
     * @param Scheduler $updateScheduler
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource,
        AbstractDb $resourceCollection,
        JsonSerializer $serializer,
        RequestInterface $request,
        Scheduler $updateScheduler,
        array $data = []
    ) {
        $this->request = $request;
        $this->updateScheduler = $updateScheduler;
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $updatedValue = $this->prepareValue($value);
            $websiteId = (int)$this->request->getParam('website', 0);
            $this->updateScheduler->scheduleUpdate($updatedValue, $websiteId);
            $this->setValue($updatedValue);
        }

        return parent::beforeSave();
    }

    /**
     * Prepare value
     *
     * @param array $value
     * @return array
     */
    private function prepareValue($value)
    {
        $updatedValue = $wasAdded = [];
        foreach ($value as $key => $item) {
            if (is_array($item) && !in_array($item['customer_group_id'], $wasAdded)) {
                $item = $this->prepareItem($item);
                if ($item[SummaryInterface::CREDIT_LIMIT] > 0) {
                    $updatedValue[$key] = $this->prepareItem($item);
                    $wasAdded[] = $item['customer_group_id'];
                }
            }
        }

        return $updatedValue;
    }

    /**
     * Prepare item
     *
     * @param array $item
     * @return array
     */
    private function prepareItem(array $item)
    {
        $item[SummaryInterface::CREDIT_LIMIT] = abs($item[SummaryInterface::CREDIT_LIMIT]);
        return $item;
    }
}
