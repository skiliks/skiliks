<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 10.03.13
 * Time: 17:47
 * To change this template use File | Settings | File Templates.
 */

namespace application\components\Logging;


class DayPlanLogTable extends LogTable
{
    public function getId()
    {
        return 'day-plan-log';
    }

    public function getTitle()
    {
        return 'Plan';
    }

    public function getHeaders()
    {
        return [
            'Графа плана', 'Время логирования состояния плана', 'Код задачи', 'Наименование задачи', 'Категория задачи',
            'Время, на которое стоит в плане', 'Сделана ли задача', 'Кол-во задач в "Сделать"'
        ];
    }

    /**
     * @param \DayPlanLog $row
     * @return array|void
     */
    protected function getRow($row)
    {
        return [
            'день ' . $row->day,
            ($row->snapshot_time == 1 ? '11:00' : '18:00'),
            $row->task->code,
            $row->task->title,
            $row->task->category,
            $row->date,
            'нет',
            $row->todo_count
        ];
    }
}