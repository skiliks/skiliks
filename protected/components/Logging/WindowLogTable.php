<?php
namespace application\components\Logging;
/**
 * \addtogroup Logging
 * @{
 */
/**
 * Universal log
 */
class WindowLogTable extends LogTable
{
    public function getHeaders()
    {
        return ['Активное окно', 'Активное подокно','Игровое время - start', 'Игровое время - end', 'Window UID', 'Time diff'];
    }

    public function getTitle()
    {
        return 'Universal';
    }

    public function getId()
    {
        return 'universal-log';
    }

    /**
     * Процессит строки
     * @param $row \LogMail
     * @return array
     */
    protected function getRow($row)
    {
        static $end_time = 0;
        $diff = ($end_time === 0)?'-':strtotime($row->start_time) - strtotime($end_time);
        $end_time = $row->end_time;
        return [
            $row->window_obj->type,
            $row->window_obj->subtype,
            $row->start_time,
            $row->end_time,
            $row->window_uid,
            $diff
        ];

    }

    /**
     * @param $logMail
     * @return string
     */
    public function getRowId($row)
    {
        return sprintf(
            'universal-log-screen-%s universal-log-sub-screen-%s universal-log-screen-and-sub-screen-%s-%s ',
            $row[0],
            $row[1],
            $row[0],
            $row[1]
        );
    }


}
