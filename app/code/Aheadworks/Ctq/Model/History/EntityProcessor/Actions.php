<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\Model\History\EntityProcessor;

use Aheadworks\Ctq\Model\History as HistoryModel;
use Magento\Framework\Serialize\Serializer\Json;
use Aheadworks\Ctq\Model\History\Actions\Converter;

/**
 * Class Actions
 * @package Aheadworks\Ctq\Model\History\EntityProcessor
 */
class Actions
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Converter $converter
     * @param Json $serializer
     */
    public function __construct(
        Converter $converter,
        Json $serializer
    ) {
        $this->converter = $converter;
        $this->serializer = $serializer;
    }

    /**
     * Convert actions data before save
     *
     * @param HistoryModel $object
     * @return HistoryModel
     */
    public function beforeSave($object)
    {
        if ($object->getActions()) {
            $actionsArray = [];
            foreach ($object->getActions() as $actionsDataModel) {
                $actionsArray[] = $this->converter->toArray($actionsDataModel);
            }
            $object->setActions($this->serializer->serialize($actionsArray));
        }

        return $object;
    }

    /**
     * Convert actions data after load
     *
     * @param HistoryModel $object
     * @return HistoryModel
     */
    public function afterLoad($object)
    {
        if ($object->getActions()) {
            $actionsDataModel = [];
            $actionsArray = $this->serializer->unserialize($object->getActions());
            foreach ($actionsArray as $actionArray) {
                $actionsDataModel[] = $this->converter->toDataModel($actionArray);
            }
            $object->setActions($actionsDataModel);
        }

        return $object;
    }
}
