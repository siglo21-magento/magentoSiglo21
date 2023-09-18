<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\ResourceModel\Quote;

use Aheadworks\Ctq\Model\Quote;
use Aheadworks\Ctq\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ctq\Model\ResourceModel\Quote as ResourceQuote;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\Data\Collection\EntityFactory as FrameworkEntityFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Aheadworks\Ctq\Model\Quote\EntityProcessor;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 * @package Aheadworks\Ctq\Model\ResourceModel\Quote
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * @var EntityProcessor
     */
    private $entityProcessor;

    /**
     * @param FrameworkEntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param EntityProcessor $entityProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        FrameworkEntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EntityProcessor $entityProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->entityProcessor = $entityProcessor;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Quote::class, ResourceQuote::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        /** @var Quote $item */
        foreach ($this as $item) {
            $this->entityProcessor->prepareDataAfterLoad($item);
        }
    }

    /**
     * Add reminder filter to collection
     *
     * @param string $expirationDate
     * @return $this
     */
    public function addWithGivenQuoteReminderDateFilter($expirationDate)
    {
        $andConditions = [
            $this->getConnection()->quoteInto(
                QuoteInterface::EXPIRATION_DATE . ' <= ?',
                $expirationDate
            ),
            QuoteInterface::REMINDER_DATE . ' IS NULL'
        ];

        $this->getSelect()->where(implode(' AND ', $andConditions));
        return $this;
    }

    /**
     * @return $this
     */
    public function addNotGivenQuoteReminderDateFilter()
    {
        $andConditions = [
            QuoteInterface::REMINDER_DATE . ' IS NOT NULL',
            $this->getConnection()->quoteInto(
                'DATE(' . QuoteInterface::REMINDER_DATE . ') <= ?',
                new \Zend_Db_Expr('CURDATE()')
            )
        ];

        $this->getSelect()->where(implode(' AND ', $andConditions));
        return $this;
    }
}
