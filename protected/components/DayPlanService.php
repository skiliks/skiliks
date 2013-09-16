<?php

/**
 * Description of DayPlanService
 */
class DayPlanService
{
    /**
     * @param Simulation $simulation
     * @return array
     */
    public static function getPlanList($simulation)
    {
        try {
            /** @var DayPlan[] $plans */
            $plans = DayPlan::model()->findAll([
                'with' => 'task',
                'condition' => 't.sim_id = :simId AND day != :todo',
                'params' => ['simId' => $simulation->id, 'todo' => DayPlan::DAY_TODO]
            ]);

            $data = array_map(function(DayPlan $plan) {
                return [
                    'date' => GameTime::getTime($plan->date),
                    'task_id' => $plan->task_id,
                    'day' => $plan->day,
                    'title' => $plan->task->title,
                    'duration' => $plan->task->duration,
                    'type' => $plan->task->is_cant_be_moved
                ];
            }, $plans);

            return ['result' => 1, 'data' => $data];

        } catch (Exception $e) {
            return ['result' => 0, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param Simulation $simulation
     * @return mixed array
     */
    public static function getTodoList(Simulation $simulation)
    {
        /** @var DayPlan[] $plans */
        $plans = DayPlan::model()->findAll([
            'with' => 'task',
            'condition' => 't.sim_id = :simId AND day = :todo',
            'params' => ['simId' => $simulation->id, 'todo' => DayPlan::DAY_TODO],
            'order'  => 't.id desc'
        ]);

        $data = array_map(function(DayPlan $plan) {
            return [
                'id'       => $plan->task->id,
                'title'    => $plan->task->title,
                'duration' => TimeTools::roundTime($plan->task->duration)
            ];
        }, $plans);

        return $data;
    }

    /**
     * @param Simulation $simulation
     * @param $taskId
     * @return array
     */
    public static function deleteTask($simulation, $taskId)
    {
        try {
            DayPlan::model()->deleteAllByAttributes([
                'sim_id' => $simulation->id,
                'task_id' => (int)$taskId
            ]);
            
            return ['result' => 1];
        } catch (Exception $e) {
            return ['result' => 0, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param Simulation $simulation
     * @param $taskId
     * @param $time
     * @param $day
     * @return array
     */
    public static function addTask(Simulation $simulation, $taskId, $day, $time = null)
    {
        /** @var Task $task */
        $task = Task::model()->findByPk($taskId);

        if (!$task || !self::_canAddTask($simulation, $task, $day, $time)) {
            return false;
        }

        $dayPlan = DayPlan::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'task_id' => $task->id
        ]);

        if (!$dayPlan) {
            $dayPlan          = new DayPlan();
            $dayPlan->sim_id  = $simulation->id;
            $dayPlan->task_id = $task->id;
        }

        $dayPlan->date = $time;
        $dayPlan->day = $day;

        return $dayPlan->save();
    }

    /**
     * Проверяет подходит ли данная задача по времени
     *
     * @param Simulation $simulation
     * @param Task $task
     * @param string $day
     * @param string $time
     * @return bool
     * @throws Exception
     */
    protected static function _canAddTask(Simulation $simulation, Task $task, $day, $time) {
        if ($day == DayPlan::DAY_1 || $day == DayPlan::DAY_2) {
            $end = GameTime::addMinutesTime($time, $task->duration);

            // Check fitting in time periods
            if ($day == DayPlan::DAY_1 && GameTime::getUnixDateTime($time) < GameTime::getUnixDateTime($simulation->getGameTime()) ||
                GameTime::getUnixDateTime($time) < GameTime::getUnixDateTime('9:00') ||
                GameTime::getUnixDateTime($end) > GameTime::getUnixDateTime('22:00')
            ) {
                return false;
            }

            // Check interception with other tasks
            $count = DayPlan::model()->count([
                'with' => ['task'],
                'condition' => 't.sim_id = :simId AND `day` = :day AND ADDTIME(`date`, SEC_TO_TIME(task.duration)) > :start AND :end > `date`',
                'params' => [
                    'simId' => $simulation->id,
                    'day' => $day,
                    'start' => $time,
                    'end' => $end
                ]
            ]);

            if ($count > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Simulation $simulation
     * @param int $minutes
     * @param int $snapShotTime
     */
    public static function copyPlanToLog($simulation, $minutes, $snapShotTime = DayPlanLog::ON_11_00)
    {
        $dayMap = [
            DayPlan::DAY_1              => DayPlanLog::TODAY,
            DayPlan::DAY_2              => DayPlanLog::TOMORROW,
            DayPlan::DAY_AFTER_VACATION => DayPlanLog::AFTER_VACATION,
            DayPlan::DAY_TODO           => DayPlanLog::TODO
        ];

        $todoCount = DayPlan::model()->countByAttributes([
            'sim_id' => $simulation->id,
            'day' => DayPlan::DAY_TODO
        ]);

        $dayPlans = DayPlan::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);
        
        foreach ($dayPlans as $planItem) {
            $log = new DayPlanLog();
            $log->uid           = $simulation->user_id;
            $log->date          = $planItem->date;
            $log->day           = $dayMap[$planItem->day];
            $log->task_id       = $planItem->task_id;
            $log->sim_id        = $simulation->id;
            $log->todo_count    = $todoCount;
            $log->snapshot_time = $snapShotTime;
            $log->save();
        }
    }

    public static function saveToXLS(Simulation $simulation)
    {
        $dayPlans = DayPlan::model()->findAllByAttributes([
            'sim_id' => $simulation->id,
            'day' => [DayPlan::DAY_1, DayPlan::DAY_2]
        ]);

        $timeMap = [];
        foreach ($dayPlans as $plan) {
            $timeMap[$plan->task_id] = [
                'day'  => $plan->day,
                'date' => $plan->date
            ];
        }

        uasort($timeMap, function($a, $b) {
            if ($a['day'] == $b['day']) {
                return strtotime($a['date']) - strtotime($b['date']);
            } else {
                return $a['day'] == DayPlan::DAY_1 ? -1 : 1;
            }
        });

        $tasks = Task::model()->findAllByAttributes([
            'id' => array_keys($timeMap)
        ]);

        /** @var Task[] $taskMap */
        $taskMap = [];
        foreach ($tasks as $task) {
            $taskMap[$task->id] = $task;
            $timeMap[$task->id]['duration'] = $task->duration;
        }

        $docTemplate = $simulation->game_type->getDocumentTemplate(['code' => 'D20']);
        /** @var MyDocument $document */
        $document = MyDocument::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'template_id' => $docTemplate->id
        ]);

        if (null === $document) {
            $document = new MyDocument();
            $document->sim_id = $simulation->id;
            $document->fileName = $docTemplate->fileName;
            $document->template_id = $docTemplate->id;
        }

        $document->hidden = 0;
        $document->save();

        // Update Plan-template with real data {
        $filepath = $document->template->getFilePath();

        /** @var PHPExcel_Reader_IReader $reader */
        $reader = PHPExcel_IOFactory::createReader('Excel5');
        $excel = $reader->load($filepath);
        $sheet = $excel->getSheetByName('Plan');

        foreach ($timeMap as $taskId => $time) {
            $row = (strtotime($time['date']) - strtotime('today') - 32400) / 900 + 3;
            $column = $time['day'] == DayPlan::DAY_1 ? 'B' : 'C';
            $height = $time['duration'] / 15 - 1;

            $sheet
                ->mergeCells($column . $row . ':' . $column . ($row + $height))
                ->setCellValue($column . $row, $taskMap[$taskId]->title)
                ->getStyle($column . $row)
                ->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
        }

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $writer->save($document->getFilePath().'.xls');

        $scData = ScXlsConverter::xls2sc($excel);
        file_put_contents($document->getFilePath(), json_encode($scData));
        // Update Plan-template with real data }

        return [
            'result'   => 1,
            'docId'    => $document->id
        ];
    }
}


