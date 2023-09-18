<?php

/**
 *
 */
namespace Aventi\SAP\Plugin;

/**
 *
 */
class LoginPostPlugin
{

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirectInterface;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * LoginPostPlugin constructor.
     * @param \Magento\Framework\App\Response\RedirectInterface $redirectInterface
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirectInterface,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->redirectInterface = $redirectInterface;
        $this->logger = $logger;
    }

    /**
      * Change redirect after login to home instead of dashboard.
      *
      * @param \Magento\Customer\Controller\Account\LoginPost $subject
      * @param \Magento\Framework\Controller\Result\Redirect $result
      * @return \Magento\Framework\Controller\Result\Redirect
      */
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result
    ) {
        $result->setPath($this->redirectInterface->getRefererUrl());
        return $result;
    }
}
