<?php
/**
 * Quick order by parte equipos
 * Copyright (C) 2018  
 * 
 * This file is part of Aventi/QuickOrder.
 * 
 * Aventi/QuickOrder is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aventi\QuickOrder\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    /**
     * @var \Magento\Customer\Block\Account\AuthorizationLink
     */
    private $authorizationLink;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Block\Account\AuthorizationLink $authorizationLink
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->authorizationLink = $authorizationLink;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //$this->messageManager->addError(__('A login and a password are required.'));


        /**Only for user loggin */
        if(!$this->authorizationLink->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addError(__('A login and a password are required.'));
            $resultRedirect->setPath('');
            return $resultRedirect;
        }



        return $this->resultPageFactory->create();
    }
}
