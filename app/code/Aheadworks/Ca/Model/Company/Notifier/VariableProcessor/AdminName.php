<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\VariableProcessor;

use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Magento\ModuleUser\UserRepository;

/**
 * Class AdminName
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\VariableProcessor
 */
class AdminName implements VariableProcessorInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var CompanyInterface $company */
        $company = $variables[EmailVariables::COMPANY];
        $variables[EmailVariables::SALES_REPRESENTATIVE_NAME] = $this->getAdminName($company);

        return $variables;
    }

    /**
     * Get admin name
     *
     * @param CompanyInterface $company
     * @return string
     */
    private function getAdminName($company)
    {
        try {
            $user = $this->userRepository->getById($company->getSalesRepresentativeId());
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = '';
        }
        return $name;
    }
}
