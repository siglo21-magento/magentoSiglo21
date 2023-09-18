<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Plugin\Block\Product;

use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\UrlInterface;

/**
 * Class AbstractProductPlugin
 * @package Aheadworks\Ctq\Plugin\Block\Product
 */
class AbstractProductPlugin
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Checker $checker
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Checker $checker,
        UrlInterface $urlBuilder
    ) {
        $this->checker = $checker;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Set return url
     *
     * @param AbstractProduct $subject
     * @param string $result
     * @param Product $product
     * @param array $additional
     * @return string
     */
    public function afterGetSubmitUrl(AbstractProduct $subject, $result, $product, $additional = [])
    {
        if ($this->checker->isQuoteList()) {
            $additional = array_merge(
                $additional,
                [
                    'id' => $subject->getRequest()->getParam('id', null),
                    Checker::AW_CTQ_QUOTE_LIST_FLAG => 1,
                    'return_url' => $this->urlBuilder->getRouteUrl('aw_ctq/quoteList/index')
                ]
            );
            $result = $this->urlBuilder->getUrl('aw_ctq/quoteList/UpdateItemOptions', $additional);
        }

        return $result;
    }
}
