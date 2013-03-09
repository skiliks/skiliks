<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 08.03.13
 * Time: 8:12
 * To change this template use File | Settings | File Templates.
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