<?php
namespace Aventi\QuickOrder\Helper;

use Magento\Customer\Model\Context;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $httpContext;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext
    )
    {
        parent::__construct($context);
        $this->httpContext = $httpContext;
    }
    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}