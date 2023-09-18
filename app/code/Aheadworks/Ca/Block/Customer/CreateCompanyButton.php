<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Block\Customer;

use Aheadworks\Ca\Model\Url;
use Magento\Framework\View\Element\Template;

/**
 * Class CreateCompanyButton
 * @package Aheadworks\Ca\Block\Customer
 */
class CreateCompanyButton extends Template
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @inheritDoc
     */
    public function __construct(
        Template\Context $context,
        Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
    }

    /**
     * Return company create page url
     *
     * @return string
     */
    public function getCreatePageUrl()
    {
        return $this->url->getFrontendCreateCompanyFormUrl();
    }
}
