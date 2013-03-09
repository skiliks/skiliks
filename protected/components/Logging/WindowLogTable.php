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
        return ['Активное окно', 'Активное подокно','Игровое время - start', 'Игровое время - end', 'Window UID'];
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
        return [
            $row->window_obj->type,
            $row->window_obj->subtype,
            $row->start_time,
            $row->end_time,
            $row->window_uid
        ];
    }
}
/**
 * @}
 */