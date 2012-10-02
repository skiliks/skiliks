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
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('Не задан sid', 1);

            // получить uid
            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception('Не могу определить пользователя', 2);

            // получить симуляцию по uid
            $simulation = Simulations::model()->byUid($uid)->find();
            if (!$simulation) throw new Exception('Не могу определить симуляцию', 3);
            
            ### обработка задач
            $task = $this->_processTasks($simulation->id);
            if ($task) {
                $result = array('result' => 1, 'data' => $task, 'eventType' => 'task');
                return $this->_sendResponse(200, CJSON::encode($result));
            }
            ###################
            
            
            $gameTime = SimulationService::getGameTime($simulation->id);
            
            // получить ближайшее событие
            Logger::debug("try to find trigger for time $gameTime sim {$simulation->id}");
            $triggers = EventsTriggers::model()->nearest($simulation->id, $gameTime)->findAll();
            
            if (count($triggers) == 0) throw new Exception('Нет ближайших событий', 4);

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
                    
                    ###################
                    // проверим событие на флаги
                    if (!$this->_allowToRunEvent($event->code, $simulation->id)) {
                        // событие не проходит по флагам -  не пускаем его
                        return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => array(), 'eventType' => 1)));
                    }
                    #####################################
                    
                    $result = EventService::processLinkedEntities($event->code, $simulation->id);
                    if ($index == 0) {
                        if ($result) return $this->_sendResponse(200, CJSON::encode($result));
                        
                        $eventCode = $event->code;
                    }
                    
                    $index++;
                }
            }
            
            if (!$eventCode) {
                return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => array(), 'eventType' => 1)));
            }
            
            /**********************
            $trigger = $triggers[0];  // получаем актуальное событие для заданной симуляции
            Logger::debug("found trigger : {$trigger->event_id}");
            
            // получить диалог
            $event = EventsSamples::model()->byId($trigger->event_id);
            if (!$event) throw new Exception('Не могу определить конкретное событие for event '.$trigger->event_id, 5);
            

            // Убиваем обработанное событие
            $trigger->delete();
            
            Logger::debug("found event : {$event->code}");
            
            $result = EventService::processLinkedEntities($event->code, $simulation->id);
            if ($result) {
                return $this->_sendResponse(200, CJSON::encode($result));
            }
            *************************************/
            
            // У нас одно событие
            
            
            Logger::debug("get dialogs by code : {$eventCode}");
            $dialogs = Dialogs::model()->byCode($eventCode)->byStepNumber(1)->findAll();
            
            $data = array();
            foreach($dialogs as $dialog) {
                Logger::debug("check dialog by code : {$dialog->code} next event : {$dialog->next_event_code}");
                
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

            

            return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data, 'eventType' => 1)));
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
    }
    
    /**
     * Проверяет а можем ли мы запускать это событие
     * @param string $code 
     * @return true
     */
    protected function _allowToRunEvent($code, $simId) {
        $ruleModel = FlagsService::getRuleByCode($code);
        if (!$ruleModel) return true; // нет правил для данного события
        
        // получим флаги для этого правила
        $flags = FlagsService::getFlags($ruleModel->id);
        if (count($flags) == 0) return true; // для данного кода нет правил
        
        // получить флаги в рамках симуляции
        $simulationFlags = SimulationService::getFlags($simId);
        if (count($simulationFlags)==0) return false; // у нас пока нет установленных флагов - не чего сравнивать
        
        // проверить на совпадение флагов с теми что есть в симуляции
        return FlagsService::compareFlags($simulationFlags, $flags);
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
                Logger::debug("create event : {$event->code}");
                
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
