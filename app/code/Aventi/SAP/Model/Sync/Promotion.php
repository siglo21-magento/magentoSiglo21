<?php

namespace Aventi\SAP\Model\Sync;

use Aventi\SAP\Helper\Attribute;
use Aventi\SAP\Helper\Data;
use Aventi\SAP\Logger\Logger;
use Aventi\SAP\Model\Integration;
use Bcn\Component\Json\Reader;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\RuleFactory;

class Promotion extends Integration
{
    const TYPE_URI = 'sales_rule';

    /**
     * @var \Aventi\SAP\Helper\Data
     */
    private $data;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    private $customerGroupCollection;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleModelFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @param Attribute $attribute
     * @param Logger $logger
     * @param DriverInterface $driver
     * @param Filesystem $filesystem
     * @param Data $data
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleFactory $ruleModelFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        Attribute $attribute,
        Logger $logger,
        DriverInterface $driver,
        Filesystem $filesystem,
        \Aventi\SAP\Helper\Data $data,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\SalesRule\Model\RuleFactory $ruleModelFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        GroupRepositoryInterface $groupRepository
    ) {
        parent::__construct($attribute, $logger, $driver, $filesystem);
        $this->data = $data;
        $this->logger = $logger;
        $this->customerGroupCollection = $customerGroupCollection;
        $this->ruleRepository = $ruleRepository;
        $this->ruleModelFactory = $ruleModelFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->groupRepository = $groupRepository;
    }

    public function updatePromotion($fast)
    {
        $start = 0;
        $rows = 1000;
        $flag = true;

        while ($flag) {
            $jsonData = $this->data->getResource(self::TYPE_URI, $start, $rows, $fast);
            $jsonPath = $this->getJsonPath($jsonData, self::TYPE_URI);
            if ($jsonPath) {
                $reader = $this->getJsonReader($jsonPath);
                $reader->enter('null', Reader::TYPE_OBJECT);
                $total = (int) $reader->read("total");
                $promotions = $reader->read("data");
                $progressBar = $this->startProgressBar($total);
                foreach ($promotions as $promotion) {
                    $promo = [
                        'sku' =>  isset($promotion['ItemCode']) ? str_replace(' ', '', $promotion['ItemCode']) : '',
                        'fromDate' => isset($promotion['FromDate']) ? $this->formatDate($promotion['FromDate']) : '',
                        'toDate' => isset($promotion['ToDate']) ? $this->formatDate($promotion['ToDate']) : '',
                        'quantity' => (int) isset($promotion['Quantity']) ? $promotion['Quantity'] : 0,
                        'discount' => (int) isset($promotion['Discount']) ? $promotion['Discount'] : 0,
                        'price' => $promotion['Price'] ?? 0,
                        'priceList' => $promotion['ListNum'] ?? 0,
                        'sortOrder' => $this->resolveSort($promotions, $promotion)
                    ];

                    $this->managerPromotion($promo);
                    $this->advanceProgressBar($progressBar);
                    //Debug only
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
    }

    public function managerPromotion($promo)
    {
        $customerGroupSelected = $this->getCustomerGroup($promo['priceList']);
        $customerGroups = $this->customerGroupCollection->toOptionArray();
        $customerGroupIds = array_keys($customerGroups);
        if ($customerGroupSelected) {
            $customerGroupIds = [$customerGroupSelected->getId()];
        }
        $name = $promo['sku'] . 'x' . $promo['priceList'] . 'x' . $promo['quantity'] . 'x' . $promo['discount'] . '%';
        $ruleCollection = $this->getSearchRule($name);
        if ($ruleCollection) {
            $checkPromo = $this->checkPromo($promo, $ruleCollection);
            if ($checkPromo) {
                return true;
            }
            $this->logger->error($ruleCollection->getRuleId());
            $rule = $this->ruleRepository->getById($ruleCollection->getRuleId());
            $rule->setToDate($promo['toDate']);
            $rule->setFromDate($promo['fromDate']);
            $rule->setDiscountAmount($promo['discount']);
            $rule->setSortOrder($promo['sortOrder']);
            $this->ruleRepository->save($rule);
        }
        if (empty($ruleCollection)) {
            $salesRule = $this->ruleModelFactory->create();
            $salesRule->setData(
                [
                    'name' => $name,
                    'description' => '',
                    'is_active' => 1,
                    'customer_group_ids' => $customerGroupIds,
                    'coupon_type' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON,
                    'simple_action' => \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION,
                    'discount_amount' => $promo['discount'],
                    'discount_step' => 0,
                    'stop_rules_processing' => 1,
                    'from_date' => $promo['fromDate'],
                    'to_date' => $promo['toDate'],
                    'is_rss' => 1,
                    'sort_order' => $promo['sortOrder'],
                    'website_ids' => [
                        1
                    ],
                    'store_labels' => [
                        0 => 'Descuento del ' . $promo['discount'] . '%',
                    ]
                ]
            );

            $salesRule->getConditions()->loadArray(
                [
                    'type' => \Magento\SalesRule\Model\Rule\Condition\Combine::class,
                    'attribute' => null,
                    'operator' => null,
                    'value' => '1',
                    'is_value_processed' => null,
                    'aggregator' => 'all',
                    'conditions' => [
                        [
                            "type" => \Magento\SalesRule\Model\Rule\Condition\Product\Subselect::class,
                            "attribute" => "qty",
                            "operator" => ">=",
                            "value" => $promo['quantity'],
                            "is_value_processed" => null,
                            "aggregator" => "all",
                            "conditions" => [
                                [
                                    "type" => \Magento\SalesRule\Model\Rule\Condition\Product::class,
                                    "attribute" => "sku",
                                    "operator" => "==",
                                    "value" => $promo['sku'],
                                    "is_value_processed" => false,
                                    "attribute_scope" => ""
                                ]
                            ]
                        ]
                    ]
                ]
            );
            $salesRule->save();
        }
        //$this->ruleRepository->save($salesRule);
    }

    /**
     * @param $data
     * @param RuleInterface $promo
     * @return bool
     */
    public function checkPromo($data, $promo)
    {
        $currentPromo = [
            'fromDate' => $data['fromDate'],
            'toDate' => $data['toDate'],
            'discount' => $data['discount'],
            'sortOrder' => $data['sortOrder']
        ];

        $headPromo = [
            'fromDate' => $promo->getFromDate(),
            'toDate' => $promo->getToDate(),
            'discount' => (int)$promo->getDiscountAmount(),
            'sortOrder' => $promo->getSortOrder()
        ];
        $checkPromo = array_diff($headPromo, $currentPromo);
        if (empty($checkPromo)) {
            return true;
        }
        return false;
    }

    /**
     * @param $date
     * @return string
     */
    public function formatDate($date)
    {
        return strtok($date, 'T');
    }

    /**
     * @param array $promotions
     * @param array $promo
     */
    private function resolveSort($promotions = [], $promo = [])
    {
        $newArray = [];
        foreach ($promotions as $promotion) {
            if ($promotion['ItemCode'] == $promo['ItemCode']) {
                $newArray[] = [
                    'ItemCode' => $promotion['ItemCode'],
                    'Quantity' => $promotion['Quantity']
                ];
            }
        }
        $orderArray = $this->array_sort($newArray, 'Quantity', SORT_ASC);
        $qty = 0;
        $count = count($orderArray) - 1;
        $priority = 0;
        foreach ($orderArray as $key => $new) {
            if ($promo['Quantity'] >= $new['Quantity']) {
                $priority = $count;
            }
            $count = $count - 1;
        }
        return $priority;
    }

    public function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = [];
        $sortable_array = [];

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    public function getSearchRule($name)
    {
        $filter1 = $this->filterBuilder
            ->setField("name")
            ->setValue($name)
            ->setConditionType("eq")->create();

        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter($filter1)->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->setFilterGroups([$filterGroup1])
            ->create();
        $items = $this->ruleRepository->getList($searchCriteria)->getItems();
        $entity = null;
        foreach ($items as $key => $item) {
            $entity = $item;
        }

        return $entity;
    }

    public function getCustomerGroup($code)
    {
        $filter1 = $this->filterBuilder
            ->setField("customer_group_code")
            ->setValue($code . " - %")
            ->setConditionType("like")->create();

        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter($filter1)->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->setFilterGroups([$filterGroup1])
            ->create();
        $items = [];
        try {
            $items = $this->groupRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            $this->logger->debug("Error to get list from customer groups: " . $e->getMessage());
        }
        $entity = null;
        foreach ($items as $key => $item) {
            $entity = $item;
        }

        return $entity;
    }
}
