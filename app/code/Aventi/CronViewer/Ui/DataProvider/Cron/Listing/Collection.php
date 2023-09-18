<?php

namespace Aventi\CronViewer\Ui\DataProvider\Cron\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect(): Collection
    {
        $this->addFilterToMap('finished_at', 'finished_at');
        $this->addFilterToMap('status', 'status');
        $this->addFilterToMap('job_code', 'job_code');
        $this->addFilterToMap('executed_at', 'executed_at');
        $this->addFilterToMap('schedule_id', 'schedule_id');
        $this->addFilterToMap('messages', 'messages');
        return parent::_initSelect();
    }
}
