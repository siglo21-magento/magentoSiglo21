<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Model\Company\Validator;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Source\Company\Status as StatusSource;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Status
 * @package Aheadworks\Ca\Model\Company\Validator
 */
class Status extends AbstractValidator
{
    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * Status constructor.
     * @param StatusSource $statusSource
     */
    public function __construct(
        StatusSource $statusSource
    ) {
        $this->statusSource = $statusSource;
    }

    /**
     * Returns true if status is valid
     *
     * @param CompanyInterface $company
     * @return bool
     */
    public function isValid($company)
    {
        if (!$this->statusSource->isValidStatus($company->getStatus())) {
            $this->_addMessages([__('Status is not correct for Company.')]);
        }

        return empty($this->getMessages());
    }
}
