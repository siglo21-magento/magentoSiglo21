<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\History\Actions;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterfaceFactory;

/**
 * Class Converter
 * @package Aheadworks\Ctq\Model\History\Actions
 */
class Converter
{
    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @param HistoryActionInterfaceFactory $historyActionFactory
     */
    public function __construct(
        HistoryActionInterfaceFactory $historyActionFactory
    ) {
        $this->historyActionFactory = $historyActionFactory;
    }

    /**
     * Convert recursive array into action data model
     *
     * @param array $input
     * @return HistoryActionInterface
     */
    public function toDataModel(array $input)
    {
        /** @var HistoryActionInterface $historyActionModel */
        $historyActionModel = $this->historyActionFactory->create();
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'type':
                    $historyActionModel->setType($value);
                    break;
                case 'status':
                    $historyActionModel->setStatus($value);
                    break;
                case 'name':
                    $historyActionModel->setName($value);
                    break;
                case 'old_value':
                    $historyActionModel->setOldValue($value);
                    break;
                case 'value':
                    $historyActionModel->setValue($value);
                    break;
                case 'actions':
                    $actions = [];
                    /** @var array $action */
                    foreach ($value as $action) {
                        $actions[] = $this->toDataModel($action);
                    }
                    $historyActionModel->setActions($actions);
                    break;
                case 'additional_information':
                    $historyActionModel->setAdditionalInformation($value);
                    break;
                default:
            }
        }
        return $historyActionModel;
    }

    /**
     * Convert recursive action data model into array
     *
     * @param HistoryActionInterface $dataModel
     * @return array
     */
    public function toArray(HistoryActionInterface $dataModel)
    {
        $output = [
            'type' => $dataModel->getType(),
            'status' => $dataModel->getStatus(),
            'name' => $dataModel->getName(),
            'old_value' => $dataModel->getOldValue(),
            'value' => $dataModel->getValue(),
            'additional_information' => $dataModel->getAdditionalInformation()
        ];

        foreach ((array)$dataModel->getActions() as $action) {
            $output['actions'][] = $this->toArray($action);
        }
        return $output;
    }
}
