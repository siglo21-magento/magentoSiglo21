<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Notifier\VariableProcessor;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Source\Company\Status;

/**
 * Class CompanyStatus
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\VariableProcessor
 */
class CompanyStatus implements VariableProcessorInterface
{
    /**
     * @var Status
     */
    private $companyStatus;

    /**
     * @param Status $companyStatus
     */
    public function __construct(Status $companyStatus)
    {
        $this->companyStatus = $companyStatus;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var CompanyInterface $company */
        $company = $variables[EmailVariables::COMPANY];
        $variables[EmailVariables::COMPANY_STATUS] = $this->companyStatus->getStatusLabel($company->getStatus());

        return $variables;
    }
}
