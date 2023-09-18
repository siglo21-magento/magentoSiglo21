<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\Request;

use Magento\Framework\App\RequestInterface;

/**
 * Class Checker
 *
 * @package Aheadworks\Ctq\Model\Request
 */
class Checker
{
    /**
     * CTQ Quote List Page Flag
     */
    const AW_CTQ_QUOTE_LIST_FLAG = 'aw_ctq_quote_list';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Check if quote list flag present
     *
     * @return bool
     */
    public function isQuoteList()
    {
        return (bool)$this->request->getParam(self::AW_CTQ_QUOTE_LIST_FLAG, false);
    }

    /**
     * Check is configure product action
     *
     * @return bool
     */
    public function isConfigureAction()
    {
        return $this->request->getActionName() === 'configure';
    }
}
