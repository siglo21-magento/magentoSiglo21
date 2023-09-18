<?php
declare(strict_types=1);

namespace Aventi\CronViewer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Cron extends AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cron_schedule', 'schedule_id');
    }
}
