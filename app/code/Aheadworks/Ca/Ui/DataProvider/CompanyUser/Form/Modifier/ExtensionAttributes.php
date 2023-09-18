<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Ui\DataProvider\CompanyUser\Form\Modifier;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Repository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class ExtensionAttributes
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier
 */
class ExtensionAttributes implements ModifierInterface
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var Repository
     */
    private $companyUserRepository;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Repository $companyUserRepository
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        Repository $companyUserRepository
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->companyUserRepository = $companyUserRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $entityFieldName = 'entity_id';
        if (!isset($data['extension_attributes'])
            && isset($data[$entityFieldName])
            && !empty($data[$entityFieldName])
        ) {
            $attributes = [
                'extension_attributes' => [
                    'aw_ca_company_user' => $this->loadCompanyUserAttributes($data[$entityFieldName])
                ]
            ];
            $data = array_merge($data, $attributes);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get company user extension attributes
     *
     * @param int $companyUserId
     * @return array
     */
    private function loadCompanyUserAttributes($companyUserId)
    {
        try {
            $companyUser = $this->companyUserRepository->get($companyUserId);
            $companyUserData = $this->dataObjectProcessor->buildOutputDataArray(
                $companyUser,
                CompanyUserInterface::class
            );
        } catch (NoSuchEntityException $exception) {
            $companyUserData = [];
        } catch (LocalizedException $exception) {
            $companyUserData = [];
        }

        return $companyUserData;
    }
}
