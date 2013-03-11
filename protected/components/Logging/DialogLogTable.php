<?php
namespace application\components\Logging;

/**
 * \addtogroup Logging
 * @{
 */

/**
 * Class DialogLogTable
 * @package application\components\Logging
 */
class DialogLogTable extends LogTable
{
    public function getId()
    {
        return 'dialog-log';
    }

    public function getTitle()
    {
        return 'Dialog log';
    }

    public function getHeaders()
    {
        return
            ['Start Time', 'End Time', 'Replica code', 'Result replica'];
    }

    protected function getRow($logDialog)
    {
        return [
            $logDialog->start_time,
            $logDialog->end_time,
            $logDialog->dialog->code,
            $logDialog->last_id
        ];
    }
}
/**
 * @}
 */