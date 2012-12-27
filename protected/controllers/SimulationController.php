<?php

/**
 * Контроллер симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationController extends AjaxController
{
    /**
     * Старт симуляции
     */
    public function actionStart()
    {
        try {
            SimulationService::simulationStart();

            $this->sendJSON(array(
                'result' => 1, 
                'speedFactor' => Yii::app()->params['skiliksSpeedFactor']
            ));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

    /**
     * Остановка симуляции
     */
    public function actionStop()
    {

        $sid = Yii::app()->request->getParam('sid', false);
        SessionHelper::setSid($sid);

        try {
            $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(array(
                    'result' => 0,
                    'e' => $e->getMessage()
                ));
        }

        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simId);
        $CheckConsolidatedBudget->calcPoints();


        $uid = SessionHelper::getUidBySid();
        if (!$uid)
            throw new Exception('Не могу найти такого пользователя');

        $simulation = Simulations::model()->byId($simId)->find();
        if ($simulation) {
            $simulation->end = time();
            $simulation->status = 0;
            $simulation->save();
        }

        // залогируем состояние плана
        DayPlanLogger::log($simId, DayPlanLogger::STOP);

        // данные для логирования

        $logs_src = Yii::app()->request->getParam('logs', false);
        $logs_src = Yii::app()->request->getParam('logs', false);
        LogHelper::setLog($simId, $logs_src);

        $logs = LogHelper::logFilter($logs_src); //Фильтр нулевых отрезков всегда перед обработкой логов
        //TODO: нужно после беты убрать фильтр логов и сделать нормальное открытие mail preview
        LogHelper::setDocumentsLog($simId, $logs); //Закрытие документа при стопе симуляции
        LogHelper::setMailLog($simId, $logs); //Закрытие ркна почты при стопе симуляции
        try {
            LogHelper::setWindowsLog($simId, $logs);
        } catch (CException $e) {
            // @todo: handle
        }
        LogHelper::setDialogs($simId, $logs);
        // make attestation 'work with emails' 
        SimulationService::saveEmailsAnalize($simId);

        // Save score for "1. Оценка ALL_DIAL"+"8. Оценка Mail Matrix"
        // see Assessment scheme_v5.pdf
        SimulationService::saveAgregatedPoints($simId);

        // @todo: this is trick
        // write all mail outbox/inbox scores to AssessmentAgregate dorectly
        SimulationService::copyMailInboxOutboxScoreToAssessmentAgregated($simId);

        $result = array('result' => 1);
        $this->sendJSON($result);
    }

    public function actionGetPoint()
    {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid)
                throw new Exception("empty sid");

            $uid = SessionHelper::getUidBySid();
            if (!$uid)
                throw new Exception("cant find user by sid {$sid}");

            $simId = SessionHelper::getSimIdBySid($sid);
            if (!$simId)
                throw new Exception("cant find simulation for sid {$sid}");

            $result = array();
            $result['result'] = 1;
            // определяем duration симуляции
            //$dialogsDuration = SimulationsDialogsDurations::model()->bySimulation($simulation->id)->find();
            $dialogsDuration = SimulationsDialogsDurations::model()->findByAttributes(array('sim_id' => $simId));
            if ($dialogsDuration) {
                $result['duration'] = $dialogsDuration->duration;
            } else {
                $result['duration'] = 0;
            }


            // загружаем поинты
            $sql = "select 
                        sdp.count,
                        sdp.value,
            
                        sdp.count_negative,
                        sdp.value_negative,    
            
                        sdp.count6x,
                        sdp.value6x,
            
                        cpt.code,
                        cpt.title
                    from simulations_dialogs_points as sdp
                    left join characters_points_titles as cpt on (cpt.id = sdp.point_id)
                    where sdp.sim_id = {$simId}";

            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);

            $dataReader = $command->query();

            foreach ($dataReader as $row) {

                $avg = 0;
                if ($row['count'] > 0)
                    $avg = $row['value'] / $row['count'];

                $avgNegative = 0;
                if ($row['count_negative'] > 0)
                    $avgNegative = $row['value_negative'] / $row['count_negative'];

                $avg6x = 0;
                if ($row['count6x'] > 0)
                    $avg6x = $row['value6x'] / $row['count6x'];

                $result['points'][] = array(
                    'code' => $row['code'],
                    'title' => $row['title'],
                    'count' => $row['count'],
                    'value' => $row['value'],
                    'avg' => $avg,
                    'countNegative' => $row['count_negative'],
                    'valueNegative' => $row['value_negative'],
                    'avgNegative' => $avgNegative,
                    'count6x' => $row['count6x'],
                    'value6x' => $row['value6x'],
                    'avg6x' => $avg6x
                );
            }

            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->sendJSON($result);
        }
    }

    /**
     * Изменение времени симуляции
     */
    public function actionChangeTime()
    {
        try {
            $newHours = (int)Yii::app()->request->getParam('hour', 0);
            $newMinutes = (int)Yii::app()->request->getParam('min', 0);
            $simulation = $this->getSimulationEntity();
            SimulationService::setSimulationClockTime(
                $simulation,
                $newHours,
                $newMinutes
            );

            $simulation->deleteOldTriggers($newHours, $newMinutes);

            $this->sendJSON(array('result' => 1));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

}

