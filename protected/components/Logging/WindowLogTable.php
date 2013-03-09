<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 08.03.13
 * Time: 7:28
 * To change this template use File | Settings | File Templates.
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