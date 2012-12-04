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
            $uid = SessionHelper::getUidBySid();
            if (!$uid) throw new Exception('Не могу определить пользователя', 2);

            // получить симуляцию по uid
            $simId = SessionHelper::getSimIdBySid($sid);
            if (!$simId) throw new Exception('Не могу определить симуляцию', 3);
            
            // данные для логирования
            $logs_src = Yii::app()->request->getParam('logs', false); 
            $logs = LogHelper::logFilter($logs_src); //Фильтр нулевых отрезков всегда перед обработкой логов
            //TODO: нужно после беты убрать фильтр логов и сделать нормальное открытие mail preview
            LogHelper::setLog($simId, $logs);
            LogHelper::setWindowsLog($simId, $logs);
            //Пишем логирование открытия и закрытия документов
            LogHelper::setDocumentsLog($simId, $logs);
            LogHelper::setMailLog($simId, $logs);
            //Yii::log($logs);
            // определим тип симуляции
            $simType = SimulationService::getType($simId);
            
            $gameTime = SimulationService::getGameTime($simId);
            
            ### обработка задач
            $task = $this->_processTasks($simId);
            if ($task) {
                $result = array('result' => 1, 'data' => $task, 'eventType' => 'task', 'serverTime' => $gameTime);
                $this->sendJSON($result);
                return;
            }
            ###################
            
            
            
            
            // получить ближайшее событие
            Logger::debug("try to find trigger for time $gameTime sim {$simId}");
            $triggers = EventsTriggers::model()->nearest($simId, $gameTime)->findAll();
            
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
                    
                    if ($index == 0) $eventCode = $event->code;
                    
                    
                    ###################
                    // проверим событие на флаги
                    Logger::debug("check flags for event by code {$event->code}");
                    if (!EventService::allowToRun($event->code, $simId, 1, 0)) {
                        // событие не проходит по флагам -  не пускаем его
                        Logger::debug("event {$event->code} was restricted by flags");
                        continue; // обрабатываем другие события
                    }
                    #####################################
                    
                    $res = EventService::processLinkedEntities($event->code, $simId);
                    if ($res) {
                        $result['events'][] = $res;
                    }
                    
                    $index++;
                }
            }
            
            
            // У нас одно событие           
            
            Logger::debug("get dialogs by code : {$eventCode}");
            $dialogs = Dialogs::model()->byCode($eventCode)->byStepNumber(1)->byDemo($simType)->findAll();
            
            $gameTime = SimulationService::getGameTime($simId);
            
            $data = array();
            foreach($dialogs as $dialog) {
                $data[(int)$dialog->excel_id] = DialogService::dialogToArray($dialog);
            }
            
            Logger::debug("src dialogs : ".var_export($data, true));
            
            // теперь подчистим список
            $resultList = $data;
            foreach ($data as $dialogId => $dialog) {
                //Logger::debug("code {$dialog['code']}, $simId, step_number {$dialog['step_number']}, replica_number {$dialog['replica_number']}");
                $flagInfo = FlagsService::checkRule($dialog['code'], $simId, $dialog['step_number'], $dialog['replica_number'], $dialogId);
                
                if ($flagInfo['ruleExists']===true && $flagInfo['compareResult'] === true && (int)$flagInfo['recId']==0) {
                    // нечего чистиить все выполняется
                    break;
                }

                //Logger::debug("flag info : ".var_export($flagInfo, true));
                if ($flagInfo['ruleExists']) {  // у нас есть такое правило
                        if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId'] > 0) {
                        // правило не выполняется для определнной записи - убьем ее
                        if (isset($resultList[ $flagInfo['recId'] ])) {
                            unset($resultList[ $flagInfo['recId'] ]);
                        }
                        continue;
                    }
                    else {
                        // правило выполняется но нужно удалить ненужную реплику
                        foreach($resultList as $key=>$val) {
                            if ($key != $flagInfo['recId'] && $val['replica_number'] == $dialog['replica_number']) {
                                unset($resultList[$key]); break;
                            }
                        }
                    }
                    
                    if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId']==0) {
                        //у нас не выполняется все событие полностью
                        $resultList = array();
                        break;
                    }
                }
                
            }
            
            $data = array();
            // а теперь пройдемся по тем кто выжил и позапускаем события
            foreach($resultList as $index=>$dialog) {
                // Если у нас реплика к герою
                if ($dialog['replica_number'] == 0) {
                    // События типа диалог мы не создаем
                    if (!EventService::isDialog($dialog['next_event_code'])) {
                        // создадим событие
                        EventService::addByCode($dialog['next_event_code'], $simId, $gameTime);
                    }
                }
                unset($resultList[$index]['step_number']);
                unset($resultList[$index]['replica_number']);
                unset($resultList[$index]['next_event_code']);
                unset($resultList[$index]['code']);
                $data[] = $resultList[$index];
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
            if (count($resultList) > 0) {
                $result['events'][] = array('result' => 1, 'eventType' => 1, 'data' => $data);
            }
           
            
            Logger::debug("result : ".var_export($result, true));
            
            $this->sendJSON($result);
        } catch (Exception $exc) {
            $this->sendJSON(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode(),
                'serverTime' => $gameTime
            ));
        }
        
        return;
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
        
        $this->sendJSON(array('result' => 1, 'data' => $data));
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
            
            $uid = SessionHelper::getUidBySid();
            if (!$uid) throw new Exception('Не могу определить пользователя');
            
            $simId = SessionHelper::getSimIdBySid($sid);
                        
            $event = EventsSamples::model()->byCode($eventCode)->find();
            if (!$event) throw new Exception('Не могу определить событие по коду : '.$eventCode);
            
            // если надо очищаем очерель событий для текущей симуляции
            if ($clearEvents) {
                EventsTriggers::model()->deleteAll("sim_id={$simId}");
            }
            
            // если надо очищаем оценки  для текущей симуляции
            if ($clearAssessment) {
                SimulationsDialogsPoints::model()->deleteAll("sim_id={$simId}");
            }
            
            $gameTime = SimulationService::getGameTime($simId);
            $gameTime = $gameTime + $delay;  //time() + ($delay/4);
            
            
            $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId($simId, $event->id)->find();
            if ($eventsTriggers) {
                Logger::debug("update event : {$event->code} set time : $gameTime id : {$event->id}");
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->save(); // обновляем существующее событие в очереди
            }
            else {
                Logger::debug("create event : code : {$event->code} id : {$event->id} sim : {$simId} time {$gameTime}");
                
                // Добавляем событие
                $eventsTriggers = new EventsTriggers();
                $eventsTriggers->sim_id = $simId;
                $eventsTriggers->event_id = $event->id;
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->insert();
            }
            
            $this->sendJSON(array('result' => 1));
            
        } catch (Exception $exc) {
            $this->sendJSON(array(
                'result' => 0, 'message' => $exc->getMessage()
            ));
        }
        return;
    }
}


