<?php



/**
 * Движек событий
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsController extends AjaxController{
    
    protected function _processTasks($simId) {
        ###  определение событие типа todo
        // получаем игровое время
        $gameTime = SimulationService::getGameTime($simId) + 9*60*60;
        // выбираем задачи из плана, которые произойдут в ближайшие 5 минут
        $toTime = $gameTime + 5*60;
        
        Logger::debug("try to find task from {$gameTime} to {$toTime}");
        $dayPlan = DayPlan::model()->nearest($gameTime, $toTime)->find();
        if (!$dayPlan) return false;
        
        // загружаем таску
        $task = Tasks::model()->byId($dayPlan->task_id)->find();
        if (!$task) return false;
        
        Logger::debug("found task {$task->id}");
        return array(
            'id' => $task->id,
            'text' => $task->title
        );
    }
    
    
    
    /**
     * Опрос состояния событий
     */
    public function actionGetState() {
        $gameTime = 0;
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('Не задан sid', 1);

            // получить uid
            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception('Не могу определить пользователя', 2);

            // получить симуляцию по uid
            $simulation = Simulations::model()->byUid($uid)->find();
            if (!$simulation) throw new Exception('Не могу определить симуляцию', 3);
            
            // определим тип симуляции
            $simType = SimulationService::getType($simulation->id);
            
            $gameTime = SimulationService::getGameTime($simulation->id);
            
            ### обработка задач
            $task = $this->_processTasks($simulation->id);
            if ($task) {
                $result = array('result' => 1, 'data' => $task, 'eventType' => 'task', 'serverTime' => $gameTime);
                return $this->_sendResponse(200, CJSON::encode($result));
            }
            ###################
            
            
            
            
            // получить ближайшее событие
            Logger::debug("try to find trigger for time $gameTime sim {$simulation->id}");
            $triggers = EventsTriggers::model()->nearest($simulation->id, $gameTime)->findAll();
            
            if (count($triggers) == 0) throw new Exception('Нет ближайших событий', 4);
            
            $result = array('result' => 1);

            $eventCode = false;
            if (count($triggers)>0) {  // если у нас много событий
                
                $index = 0;
                Logger::debug("process triggers");
                foreach($triggers as $trigger) {
                    
                    $event = EventsSamples::model()->byId($trigger->event_id)->find();
                    if (!$event) throw new Exception('Не могу определить конкретное событие for event '.$trigger->event_id, 5);
                    
                    Logger::debug("found event by code {$event->code}");
                    
                    // Убиваем обработанное событие
                    $trigger->delete();
                    
                    if ($index == 0) {
                        //if ($result) return $this->_sendResponse(200, CJSON::encode($result));
                        $eventCode = $event->code;
                    }
                    
                    ###################
                    // проверим событие на флаги
                    Logger::debug("check flags event by code {$event->code}");
                    if (!EventService::allowToRun($event->code, $simulation->id, 1, 0)) {
                        // событие не проходит по флагам -  не пускаем его
                        //return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => array(), 'eventType' => 1)));
                        continue; // обрабатываем другие события
                    }
                    #####################################
                    
                    $res = EventService::processLinkedEntities($event->code, $simulation->id);
                    if ($res) {
                        $result['events'][] = $res;
                    }
                    
                    $index++;
                }
            }
            
            
            // У нас одно событие
            
            
            Logger::debug("get dialogs by code : {$eventCode}");
            $dialogs = Dialogs::model()->byCode($eventCode)->byStepNumber(1)->byDemo($simType)->findAll();
            
            $data = array();
            foreach($dialogs as $dialog) {
                Logger::debug("check dialog by code : {$dialog->code} next event : {$dialog->next_event_code}");
                
                if (FlagsService::skipReplica($dialog, $simulation->id)) continue;  // если реплика не проходи по флагам
                
                // Если у нас реплика к герою
                if ($dialog->replica_number == 0) {
                    // События типа диалог мы не создаем
                    if (!EventService::isDialog($dialog->next_event_code)) {
                        // создадим событие
                        EventService::addByCode($dialog->next_event_code, $simulation->id, SimulationService::getGameTime($simulation->id));
                    }
                }
                $data[] = DialogService::dialogToArray($dialog);
            }
            
            if (isset($data[0]['ch_from'])) {
                $characterId = $data[0]['ch_from'];
                $character = Characters::model()->byId($characterId)->find();
                if ($character) {
                    $data[0]['title'] = $character->title;
                    $data[0]['name'] = $character->fio;
                }
            }

            $result['serverTime'] = $gameTime;
            if (count($data) > 0) {
                $result['events'][] = array(
                        'result' => 1,
                        'eventType' => 1,
                        'data' => $data
                    );
            }
            
            //$result['data'] = $data;
            //$result['eventType'] = 1;
            
            Logger::debug("result : ".var_export($result, true));
            
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode(),
                'serverTime' => $gameTime
            )));
        }
    }
    
    
    
    /**
     * Возврат списка доступных событий в системе
     */
    public function actionGetList() {
        $eventsSamples = EventsSamples::model()->findAll();
        $data = array();
        foreach($eventsSamples as $event) {
            $data[] = array(
                'id' => $event->id,
                'code' => $event->code,
                'title' => $event->title
            );
        }
        
        $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data)));
    }
    
    /**
     * Принудительный старт заданного события
     */
    public function actionStart() {
        $sid = Yii::app()->request->getParam('sid', false);  
        $eventCode = Yii::app()->request->getParam('eventCode', false);  
        $delay = (int)Yii::app()->request->getParam('delay', false);  
        $clearEvents = Yii::app()->request->getParam('clearEvents', false);  
        $clearAssessment = Yii::app()->request->getParam('clearAssessment', false);  
        
        try {
            if (!$sid) throw new Exception('Не задан сид');
            
            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception('Не могу определить пользователя');
            
            $simulation = Simulations::model()->byUid($uid)->find();
            if (!$simulation) throw new Exception('Не могу определить симуляцию');
            
            $event = EventsSamples::model()->byCode($eventCode)->find();
            if (!$event) throw new Exception('Не могу определить событие по коду : '.$eventCode);
            
            // если надо очищаем очерель событий для текущей симуляции
            if ($clearEvents) {
                EventsTriggers::model()->deleteAll("sim_id={$simulation->id}");
            }
            
            // если надо очищаем оценки  для текущей симуляции
            if ($clearAssessment) {
                SimulationsDialogsPoints::model()->deleteAll("sim_id={$simulation->id}");
            }
            
            $gameTime = SimulationService::getGameTime($simulation->id);
            $gameTime = $gameTime + $delay;  //time() + ($delay/4);
            
            
            $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
            if ($eventsTriggers) {
                Logger::debug("update event : {$event->code}");
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->save(); // обновляем существующее событие в очереди
            }
            else {
                Logger::debug("create event : code : {$event->code} id : {$event->id} sim : {$simulation->id} time {$gameTime}");
                
                // Добавляем событие
                $eventsTriggers = new EventsTriggers();
                $eventsTriggers->sim_id = $simulation->id;
                $eventsTriggers->event_id = $event->id;
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->insert();
            }
            
            return $this->_sendResponse(200, CJSON::encode(array('result' => 1)));
            
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0, 'message' => $exc->getMessage()
            )));
        }
    }
}

?>
