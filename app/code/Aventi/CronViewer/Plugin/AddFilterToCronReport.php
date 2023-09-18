<?php

namespace Aventi\CronViewer\Plugin;

use Aventi\CronViewer\Ui\DataProvider\Cron\ListingDataProvider as CronDataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class AddFilterToCronReport
{
    public function afterGetSearchResult(CronDataProvider $subject, SearchResult $result)
    {
        $result->getSelect()->where('job_code LIKE "%aventi%"');
        $result->addOrder('scheduled_at', 'DESC');
        return $result;
    }
}
