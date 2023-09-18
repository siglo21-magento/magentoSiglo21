<?php

namespace Aventi\PriceList\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PriceList extends AbstractHelper
{
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
     * @var \Aventi\PriceList\Api\PriceListRepositoryInterface
     */
    private $priceListRepository;
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * PriceList constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Aventi\PriceList\Api\PriceListRepositoryInterface $priceListRepository
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Aventi\PriceList\Api\PriceListRepositoryInterface $priceListRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->priceListRepository = $priceListRepository;
        $this->groupRepository = $groupRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param $sku
     * @param $list
     * @return string|null
     * @throws LocalizedException
     */
    public function getPriceBySkuAndList($sku, $list)
    {
        $filter1 = $this->filterBuilder
          ->setField("sku")
          ->setValue($sku)
          ->setConditionType("eq")->create();

        $filterGroup1 = $this->filterGroupBuilder
          ->addFilter($filter1)->create();

        $filter2 = $this->filterBuilder
          ->setField("group")
          ->setValue($list)
          ->setConditionType("eq")->create();

        $filterGroup2 = $this->filterGroupBuilder
          ->addFilter($filter2)->create();

        $searchCriteria = $this->searchCriteriaBuilder
          ->setFilterGroups([$filterGroup1, $filterGroup2])
          ->create();
        $items = $this->priceListRepository->getList($searchCriteria)->getItems();

        $price = null;
        foreach ($items as $key => $item) {
            $price = $item->getPrice();
        }

        return $price;
    }

    /**
     * @param $groupId
     * @return \Magento\Customer\Api\Data\GroupInterface|null
     */
    public function getGroup($groupId)
    {
        try {
            return $this->groupRepository->getById($groupId);
        } catch (NoSuchEntityException $e) {
            return null;
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer($customerId)
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return null;
        } catch (LocalizedException $e) {
            return null;
        }
    }
}
