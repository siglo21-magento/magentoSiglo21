<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Block;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class ButtonWithCheckAcl
 *
 * @method string getLink()
 * @method string setLink($link)
 * @method string getAdditionalClasses()
 * @method string getLabel()
 * @package Aheadworks\Ca\Block
 */
class ButtonWithCheckAcl extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Ca::button_with_check_acl.phtml';

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * ButtonWithCheckAcl constructor.
     * @param Context $context
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        AuthorizationManagementInterface $authorizationManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $path = $this->createPathFromLink($this->getLink());
        if (!$this->authorizationManagement->isAllowed($path)) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Escape link and create path for acl
     * @param $link
     * @return string
     */
    private function createPathFromLink($link)
    {
        $path = trim(
            parse_url($link, PHP_URL_PATH),
            '/'
        );
        $asArray = explode('/', $path);
        $path = implode('/', array_slice($asArray, 0, 3));

        return $path;
    }
}
