<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Ui\Component\Listing\Customer\Bookmark;

use Magento\User\Model\User as AdminUser;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Ui\Api\Data\BookmarkInterface;
use Magento\Ui\Api\Data\BookmarkInterfaceFactory;

/**
 * Class OutstandingBalance
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Customer\Bookmark
 */
class OutstandingBalance
{
    /**#@+
     * Bookmark settings
     */
    const NAMESPACE_NAME = 'aw_credit_limit_customer_listing';
    const IDENTIFIER = 'aw_credit_limit_outstanding_balances';
    /**#@-*/

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var BookmarkInterfaceFactory
     */
    private $bookmarkFactory;

    /**
     * @param BookmarkInterfaceFactory $bookmarkFactory
     * @param JsonSerializer $serializer
     */
    public function __construct(
        BookmarkInterfaceFactory $bookmarkFactory,
        JsonSerializer $serializer
    ) {
        $this->bookmarkFactory = $bookmarkFactory;
        $this->serializer = $serializer;
    }

    /**
     * Create outstanding balances bookmark
     *
     * @param AdminUser $user
     * @return BookmarkInterface
     */
    public function create($user)
    {
        $title = __('Outstanding balances');
        $config = [
            'views' => [
                self::IDENTIFIER => [
                    'index' => self::IDENTIFIER,
                    'label' => $title,
                    'data' => $this->getGridConfigData()
                ]
            ]
        ];

        /** @var BookmarkInterface $bookmark */
        $bookmark = $this->bookmarkFactory->create();
        $bookmark
            ->setUserId($user->getId())
            ->setNamespace(self::NAMESPACE_NAME)
            ->setIdentifier(self::IDENTIFIER)
            ->setTitle($title)
            ->setCurrent(false)
            ->setConfig($this->serializer->serialize($config));

        return $bookmark;
    }

    /**
     * Get grid config data
     *
     * @return array
     */
    public function getGridConfigData()
    {
        return [
            "filters" => [
                'applied' => [
                    "placeholder" => true,
                    "credit_balance" => ["to" => "-0.01"]
                ]
            ],
            "columns" => [
                "customer_id"        => ["visible" => true, "sorting" => false],
                "customer_email"     => ["visible" => true, "sorting" => false],
                "credit_limit"       => ["visible" => true, "sorting" => false],
                "aw_ca_company"      => ["visible" => true, "sorting" => false],
                "aw_ca_is_activated" => ["visible" => true, "sorting" => false],
                "credit_balance"     => ["visible" => true, "sorting" => "asc"],
                "credit_available"   => ["visible" => true, "sorting" => false],
                "actions"            => ["visible" => true, "sorting" => false],
                "group_id"           => ["visible" => true, "sorting" => false],
                "last_payment_date"  => ["visible" => true, "sorting" => false],
                "customer_name"      => ["visible" => true, "sorting" => false]
            ],
            "paging" => [
                "options" => [
                    "20"  => ["value" => 20, "label" => 20],
                    "30"  => ["value" => 30, "label" => 30],
                    "50"  => ["value" => 50, "label" => 50],
                    "100" => ["value" => 100, "label" => 100],
                    "200" => ["value" => 200, "label" => 200]
                ],
                "value"    => 20,
                "pageSize" => 20,
                "current"  => 1
            ],
            "displayMode" => "grid",
            "positions" => [
                "customer_id"        => 0,
                "customer_name"      => 1,
                "aw_ca_company"      => 2,
                "aw_ca_is_activated" => 3,
                "group_id"           => 4,
                "customer_email"     => 5,
                "credit_limit"       => 6,
                "credit_balance"     => 7,
                "credit_available"   => 8,
                "last_payment_date"  => 9,
                "actions"            => 10
            ]
        ];
    }
}
