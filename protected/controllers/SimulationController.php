<?php

/**
 * Контроллер симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationController extends AjaxController{
    
    protected function _fillDayPlan($simId) {
        $tasks = array(14, 15, 19, 8, 17);  //, 21
        
        $tasks = Tasks::model()->byIds($tasks)->findAll();
        
        foreach($tasks as $task) {
            
            $date = $task->start_time;
            
            $dayPlan = new DayPlan();
            $dayPlan->sim_id = $simId;
            $dayPlan->task_id = $task->id;
            $dayPlan->date = $date;
            $dayPlan->day = 1; //rand(1, 2);
            $dayPlan->insert();
                    
            
        }
    }
    
    /**
     * Предустановка задач в симуляции
     * @param type $simId 
     */
    protected function _fillTodo($simId) {
        //$tasks = array(1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 16, 18);
        
        $tasks = Tasks::model()->byStartType('start')->findAll();
        
        foreach($tasks as $task) {
            if ($task->code != 'P017') {
                TodoService::add($simId, $task->id);
            }
            else {
                $dayPlan = new DayPlan();
                $dayPlan->sim_id    = $simId;	
                $dayPlan->date      = $task->start_time;	
                $dayPlan->day       = 1;	
                $dayPlan->task_id   = $task->id;
                $dayPlan->insert();
            }
        }
    }
    
    protected function _createEventByCode($code, $simId) {
        $event = EventsSamples::model()->byCode($code)->find();
        if ($event) {
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id         = $simId;
            $eventsTriggers->event_id       = $event->id;
            $eventsTriggers->trigger_time   = $event->trigger_time; 
            $eventsTriggers->save();
        }
    }
    
    /**
     * Старт симуляции
     */
    public function actionStart() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            $stype = (int)Yii::app()->request->getParam('stype', false); // тип симуляции 1 - promo, 2 - dev

            $uid = SessionHelper::getUidBySid();
            if (!$uid) throw new Exception('Не могу найти такого пользователя');

            // @todo: проверить а имеет ли право пользователь на эту симуляцию
            if (!UserService::isMemberOfGroup($uid, $stype)) {
                throw new Exception('У вас нет прав для старта этой симуляции');
            }

            // Создаем новую симуляцию
            $simulation = new Simulations();
            $simulation->user_id    = $uid;
            $simulation->status     = 1;
            $simulation->start      = time();
            $simulation->difficulty = 1;
            $simulation->type       = $stype;
            $simulation->insert();

            $simId = $simulation->id;
            session_id($sid);
            Yii::app()->session['simulation'] = $simId;

            // Сделать вставки в events triggers
            $events = EventsSamples::model()->findAll();  // limit(1)->
            foreach($events as $event) {

                if (EventService::isPlan($event->code)) continue;
                if (EventService::isDocument($event->code)) continue;
                if (EventService::isSendedMail($event->code)) continue;
                if (EventService::isMessageYesterday($event->code)) continue;
                //if (false === EventService::allowToRun($event->code, $simId, 1, 0)) continue;

                // событие создаем только если для него задано время
                if ($event->trigger_time > 0) {
                    $eventsTriggers = new EventsTriggers();
                    $eventsTriggers->sim_id         = $simId;
                    $eventsTriggers->event_id       = $event->id;
                    $eventsTriggers->trigger_time   = $event->trigger_time;
                    $eventsTriggers->save();
                }
            }

            // предустановка задач в todo!
            $this->_fillTodo($simId);

            // скопируем документы
            MyDocumentsService::init($simId);

            // Установим дефолтовые значения для почтовика
            MailBoxService::initDefaultSettings($simId);

            // проставим дефолтовые значени флагов для симуляции пользователя
            FlagsService::initDefaultValues($simId);

            $result = array('result' => 1, 'speedFactor' => Yii::app()->params['skiliksSpeedFactor']);
            $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            $this->sendJSON($result);
        }
        return;
    }
    
    /**
     * Остановка симуляции
     */
    public function actionStop() {
        
        $sid = Yii::app()->request->getParam('sid', false);
        SessionHelper::setSid($sid);
            
        try {
           $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(array(
                'result' => 0,
                'e'      => $e->getMessage()
            ));
        }

        $CheckConsolidatedBudget = new CheckConsolidatedBudget($simId);
        $CheckConsolidatedBudget->calcPoints();
        
        
        $uid = SessionHelper::getUidBySid();
        if (!$uid) throw new Exception('Не могу найти такого пользователя');        
        
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
        LogHelper::setDocumentsLog($simId, $logs);//Закрытие документа при стопе симуляции
        LogHelper::setMailLog($simId, $logs);//Закрытие ркна почты при стопе симуляции
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
    
    public function actionGetPoint() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("empty sid");
            
            $uid = SessionHelper::getUidBySid();
            if (!$uid) throw new Exception("cant find user by sid {$sid}");

            $simId = SessionHelper::getSimIdBySid($sid);
            if (!$simId) throw new Exception("cant find simulation for sid {$sid}");
            
            $result = array();
            $result['result'] = 1;
            // определяем duration симуляции
            //$dialogsDuration = SimulationsDialogsDurations::model()->bySimulation($simulation->id)->find();
            $dialogsDuration = SimulationsDialogsDurations::model()->findByAttributes(array('sim_id'=>$simId));
            if ($dialogsDuration) {
                $result['duration'] = $dialogsDuration->duration;    
            }
            else {
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
        
            foreach($dataReader as $row) { 
                
                $avg = 0;
                if ($row['count'] > 0) $avg = $row['value'] / $row['count'];
                
                $avgNegative = 0;
                if ($row['count_negative'] > 0) $avgNegative = $row['value_negative'] / $row['count_negative'];
                
                $avg6x = 0;
                if ($row['count6x'] > 0) $avg6x = $row['value6x'] / $row['count6x'];
                
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
    public function actionChangeTime() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("empty sid");

            $uid = SessionHelper::getUidBySid();
            if (!$uid) throw new Exception("cant find user by sid {$sid}");

            $simulation = Simulations::model()->byUid($uid)->nearest()->find();
            if (!$simulation) throw new Exception("cant find simulation for uid {$uid}");
           
            $hour = (int)Yii::app()->request->getParam('hour', false);
            $min = (int)Yii::app()->request->getParam('min', false);
            

            $variance = time() - $simulation->start;
            $variance = $variance * Yii::app()->params['skiliksSpeedFactor'];

            $unixtimeMins = round($variance/60);
            $clockH = round($unixtimeMins/60);
            $clockM = $unixtimeMins-($clockH*60);
            $clockH = $clockH + 9;

            $simulation->start = ($simulation->start - (($hour-$clockH)*60*60 / Yii::app()->params['skiliksSpeedFactor'])
                - (($min-$clockM)*60 / Yii::app()->params['skiliksSpeedFactor']));
            
            $simulation->save();
            
            $result = array('result' => 1);
            $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            $this->sendJSON($result);
        }
    }
}


