<?php

/**
 * Сервис  по работе с симуляциями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationService 
{
    /**
     * Определение типа симуляции
     * @param int $sid
     * @return int
     */
    public static function getType($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) return false;
        return $simulation->type;
    }
    
    public static function getUid($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) return false;
        return $simulation->user_id;
    }

    /**
     * Определяет игровое время в рамках заданной симуляции
     * @param int $simId
     * @throws Exception
     * @return int игровое время
     */
    public static function getGameTime($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        return $simulation->getGameTime();
    }
    
    /**
     * Установка флага в рамках симуляции
     * @param int $simId
     * @param string $flag 
     */
    public static function setFlag($simId, $flag) {
        $model = SimulationFlagsModel::model()->bySimulation($simId)->byFlag($flag)->find();
        if (!$model) {
            $model = new SimulationFlagsModel();
            $model->sim_id = $simId;
            $model->flag = $flag;
        }
        
        $model->value = 1;
        $model->save();
    }
    
    /**
     * Получить список флагов в рамках симуляции
     * @param int $simId
     * @return array
     */
    public static function getFlags($simId) {
        $flags = SimulationFlagsModel::model()->bySimulation($simId)->findAll();
        
        $list = array();
        foreach($flags as $flag) {
            $list[$flag->flag] = $flag->value;
        }
        
        return $list;
    }
    
    /**
     * Save results of "work with emails"
     * 
     * @param integer $simId
     */
    public static function saveEmailsAnalize($simId) 
    {
        // init emails in analizer
        $emailAnalizer = new EmailAnalizer($simId);
        
        // 3322_3324 {
        // 3322 - add to plan right tasks
        // 3324 - add to plan wrong tasks        
        
        $b_3322_3324 = $emailAnalizer->check_3322_3324();
        
        if (isset($b_3322_3324['3322']) && 
            isset($b_3322_3324['3322']['obj']) && 
            isset($b_3322_3324['3322']['positive']) &&
            true === $b_3322_3324['3322']['obj'] instanceof CharactersPointsTitles) 
            {
            $emailResultsFor_3322 = new SimulationsMailPointsModel();
            $emailResultsFor_3322->sim_id        = $simId;
            $emailResultsFor_3322->point_id      = $b_3322_3324['3322']['obj']->id;
            $emailResultsFor_3322->scale_type_id = $b_3322_3324['3322']['obj']->type_scale;
            $emailResultsFor_3322->value         = $b_3322_3324['3322']['positive'];
            try {
                $emailResultsFor_3322->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
            
        if (isset($b_3322_3324['3324']) && 
            isset($b_3322_3324['3324']['obj']) && 
            isset($b_3322_3324['3324']['negative']) &&
            true === $b_3322_3324['3324']['obj'] instanceof CharactersPointsTitles)  
            {
            $emailResultsFor_3324 = new SimulationsMailPointsModel();
            $emailResultsFor_3324->sim_id        = $simId;
            $emailResultsFor_3324->point_id      = $b_3322_3324['3324']['obj']->id;
            $emailResultsFor_3324->scale_type_id = $b_3322_3324['3324']['obj']->type_scale;
            $emailResultsFor_3324->value         = $b_3322_3324['3324']['negative'];
            try {
                $emailResultsFor_3324->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        // 3322_3324 }
        
        //3325 - read spam {        
        $b_3325 = $emailAnalizer->check_3325();        
            
        if (isset($b_3325['obj']) && 
            isset($b_3325['negative']) &&
            true === $b_3325['obj'] instanceof CharactersPointsTitles)  
            {

            $emailResultsFor_3325 = new SimulationsMailPointsModel();
            $emailResultsFor_3325->sim_id        = $simId;
            $emailResultsFor_3325->point_id      = $b_3325['obj']->id;
            $emailResultsFor_3325->scale_type_id = $b_3325['obj']->type_scale;
            $emailResultsFor_3325->value         = $b_3325['negative'];
            try {
                $emailResultsFor_3325->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3325 - read spam }

        //3323 - any action for 2 minutes tasks {        
        $b_3323 = $emailAnalizer->check_3323();
            
        if (isset($b_3323['obj']) && 
            isset($b_3323['positive']) &&
            true === $b_3323['obj'] instanceof CharactersPointsTitles)  
            {
            $emailResultsFor_3323 = new SimulationsMailPointsModel();
            $emailResultsFor_3323->sim_id        = $simId;
            $emailResultsFor_3323->point_id      = $b_3323['obj']->id;
            $emailResultsFor_3323->scale_type_id = $b_3323['obj']->type_scale;
            $emailResultsFor_3323->value         = $b_3323['positive'];
            try {
                $emailResultsFor_3323->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3323 - any action for 2 minutes tasks }        

        //3313 - read most of not-spam emails {        
        $b_3313 = $emailAnalizer->check_3313();
            
        if (isset($b_3313['obj']) && 
            isset($b_3313['positive']) &&
            true === $b_3313['obj'] instanceof CharactersPointsTitles)  
            {
            $emailResultsFor_3313 = new SimulationsMailPointsModel();
            $emailResultsFor_3313->sim_id        = $simId;
            $emailResultsFor_3313->point_id      = $b_3313['obj']->id;
            $emailResultsFor_3313->scale_type_id = $b_3313['obj']->type_scale;
            $emailResultsFor_3313->value         = $b_3313['positive'];
            try {
                $emailResultsFor_3313->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }

        
        $b_3333 = $emailAnalizer->check_3333();
        if (isset($b_3333['obj']) && 
            isset($b_3333['positive']) &&
            true === $b_3333['obj'] instanceof CharactersPointsTitles)  
            {
            $emailResultsFor_3333 = new SimulationsMailPointsModel();
            $emailResultsFor_3333->sim_id = $simId;
            $emailResultsFor_3333->point_id = $b_3333['obj']->id;
            $emailResultsFor_3333->scale_type_id = $b_3333['obj']->type_scale;
            $emailResultsFor_3333->value = $b_3333['positive'];
            try {
                $emailResultsFor_3333->save();
            } catch (Exception $e) {
                // @todo: handle exception
            }
        }
        //3313 - read most of not-spam emails } 
    }
    
    /**
     * @param integer $simId
     * @return array of BehaviourCounter
     */
    public static function getAgregatedPoints($simId) 
    {
        // @todo: fix this relation to logHelper
        $data = LogHelper::getDialogPointsDetail(LogHelper::RETURN_DATA, array('sim_id' => $simId));
        
        $behaviours = array();
        
        /**
         * $line:
            'p_code'           => 'Номер поведения',
            'add_value'      => 'Проявление',
         */
          
        foreach ($data['data'] as $line) {
            $pointCode = $line['code'];
            if (false === isset($behaviours[$pointCode])) {
                $behaviours[$pointCode] = new BehaviourCounter();
            }
            
            $behaviours[$pointCode]->update($line['add_value']);
        }
  
        // add Point object
        foreach (CharactersPointsTitles::model()->byIsBehaviour()->findAll() as $point) {
            if (isset($behaviours[$point->code])) {
                $behaviours[$point->code]->mark = $point;
            }
        }  
        
        return $behaviours;
    }
    
    /**
     * @param integer $simId
     */    
    public static function saveAgregatedPoints($simId) 
    {

        foreach(self::getAgregatedPoints($simId) as $agrPoint) {
            // check, is in some fantastic way such value exists in DB {
            $existAssassment = AssessmentAggregated::model()
                ->bySimId($simId)
                ->byPoint($agrPoint->mark->id)
                ->find();
            // check, is in some fantastic way such value exists in DB }
            
            // init Log record {
            if (null == $existAssassment) {
                $existAssassment = new AssessmentAggregated();
                $existAssassment->sim_id   = $simId;
                $existAssassment->point_id = $agrPoint->mark->id;
            }
            // init Log record }
            
            // set value
            $existAssassment->value = $agrPoint->getValue();
            if ($agrPoint->mark->isNegative() && 0 < $existAssassment->value) {
                // fix for negative points
                $existAssassment->value =-$existAssassment->value;
            }
            
            $existAssassment->save();
        }

        //3313 - read most of not-spam emails } 
    }
    
    /**
     * @param integer $simId
     */ 
    public static function copyMailInboxOutboxScoreToAssessmentAgregated($simId)
    {
        // add mail inbox/outbox points
        foreach (SimulationsMailPointsModel::model()->bySimulation($simId)->findAll() as $emailBehaviour) {
            $assassment = new AssessmentAggregated();
            $assassment->sim_id   = $simId;
            $assassment->point_id = $emailBehaviour->point_id;
            $assassment->value = $emailBehaviour->value;
            $assassment->save();
        }
    }
    
    /**
     * must be called at once, when simulation starts
     * @param integer $simulationId
     */
    public static function fillTodo($simulationId)
    {
        $tasks = Tasks::model()->byStartType('start')->findAll();

        foreach ($tasks as $task) {
            // @todo: crazy tweak, works for current SimScenario only
            if ($task->code != 'P017') {
                // @todo: add attribute 'is_predefined' for task model.
                // set it true for 'P017'
                TodoService::add($simulationId, $task->id);
            } else {
                $dayPlan = new DayPlan();
                $dayPlan->sim_id  = $simulationId;
                $dayPlan->date    = $task->start_time;
                $dayPlan->day     = 1;
                $dayPlan->task_id = $task->id;
                $dayPlan->insert();
            }
        }
    }
    
    /**
     * @param integer $userId
     * @param integer $simulationType
     * 
     * @return Simulations
     */
    public static function initSimulationEntity($userId, $simulationType)
    {
        $simulation = new Simulations();
        $simulation->user_id = $userId;
        $simulation->status = 1;
        $simulation->start = time();
        $simulation->difficulty = 1;
        $simulation->type = $simulationType;
        $simulation->insert();
        
        return $simulation;
    }
    
    /**
     * @param Simulation $simulation
     * 
     * @return array of EventsTriggers
     */
    public static function initEventTriggers($simulation)
    {
        $events = EventsSamples::model()
            ->byNotDocumentCode()
            ->byNotPlanTaskCode()
            ->byNotSendedTodayEmailCode()
            ->byNotSendedYesterdayEmailCode()
            ->byNotTerminatorCode()
            ->byTriggerTimeGreaterThanZero()
            ->findAll();
        
        $initedEvents = array();
        $i = 0;
        foreach ($events as $event) {
            $initedEvents[$i] = new EventsTriggers();
            $initedEvents[$i]->sim_id = $simulation->id;
            $initedEvents[$i]->event_id = $event->id;
            $initedEvents[$i]->trigger_time = $event->trigger_time;
            $initedEvents[$i]->save();
            $i++;
        }

        return $initedEvents;
    }
    
    public static function simulationStart()
    {
        // тип симуляции 1 - promo, 2 - dev
        $simulationType = (int) Yii::app()->request->getParam('stype', 1); 

        $userId = SessionHelper::getUidBySid();
        if (false === UserService::isMemberOfGroup($userId, $simulationType)) {
            throw new Exception('У вас нет прав для старта этой симуляции');
        }
        
        // Создаем новую симуляцию
        $simulation = SimulationService::initSimulationEntity($userId, $simulationType);
        
        // save simulation ID to user session
        Yii::app()->session['simulation'] = $simulation->id;

        //@todo: increase speed
        SimulationService::initEventTriggers($simulation); // 3 seconds 

        // предустановка задач в todo!
        SimulationService::fillTodo($simulation->id);

        // скопируем документы
        MyDocumentsService::init($simulation->id);

        // @todo: increase speed
        // Установим дефолтовые значения для mail client
        MailBoxService::initDefaultSettings($simulation->id); // 4 seconds
        
        // Copy email templates
        MailBoxService::initMailBoxEmails($simulation->id);

        // проставим дефолтовые значени флагов для симуляции пользователя
        FlagsService::initDefaultValues($simulation->id);
        
        return $simulation;
    }
    
    /**
     * WTF! This crazy code not change internal sim time? but change sim start value
     * in real life time coords
     *
     * There are no internal simulation time stored anywhere :)
     * 
     * @param Simulations $simulation
     * @param integer $newHours
     * @param integer $newMinutes
     */
    public static function setSimulationClockTime($simulation, $newHours, $newMinutes)
    {
        $speedFactor = Yii::app()->params['skiliksSpeedFactor'];
        
        $variance = time() - $simulation->start;
        $variance = $variance * $speedFactor;

        $unixtimeMins = round($variance / 60);
        $start_time = explode(':', Yii::app()->params['simulationStartTime']);
        $clockH = round($unixtimeMins / 60);
        $clockM = $unixtimeMins - ($clockH * 60);
        $clockH = $clockH + $start_time[0];
        $clockM = $clockM + $start_time[1];

        $simulation->start = ($simulation->start - (($newHours - $clockH) * 60 * 60 / $speedFactor)
            - (($newMinutes - $clockM) * 60 / $speedFactor));

        $simulation->save();
    }
}
