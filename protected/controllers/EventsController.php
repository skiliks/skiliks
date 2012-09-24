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
     * Обработка связанных сущностей типа почты, плана...
     * @param type $dialog
     * @return type 
     */
    protected function _processLinkedEntities($eventCode, $simId) {
        // анализ писем
        $code = false;
        $type = false;
        
        Logger::debug("_processLinkedEntities : code : {$eventCode}");
        if (preg_match_all("/MY(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'MY'; // Message Yesterday
        }
        
        if (preg_match_all("/M(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'M'; // входящие письма
        }
        
        if (preg_match_all("/MSY(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'MSY'; // входящие письма
        }
        
        if (preg_match_all("/MS(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'MS'; // входящие письма
        }
        
        if (preg_match_all("/D(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'D'; // документ
        }
        
        if (preg_match_all("/P(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'P'; // задача в плане
        }
        
        if (!$code) return false; // у нас нет связанных сущностей
        
        $result = false;
        if ($type == 'MY') {
            // отдать письмо по коду
            $mailModel = MailBoxModel::model()->byCode($code)->find();
            if ($mailModel) {
                // если входящее письмо УЖЕ пришло (кодировка MY - Message Yesterday)
                //  - то в списке писем должно быть выделено именно это письмо
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'M') {
            // если входящее письмо не пришло (кодировка M) - то указанное письмо должно прийти
            $mailModel = MailBoxService::copyMessageFromTemplateByCode($simId, $code);
            //$mailModel = MailBoxModel::model()->byCode($code)->find();
            if ($mailModel) {
                $mailModel->group_id = 1; //входящие
                $mailModel->save();
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'MSY') {
            // отдать письмо по коду
            $mailModel = MailBoxModel::model()->byCode($code)->find();
            if ($mailModel) {
                // если исходящее письмо уже отправлено  (кодировка MSY - Message Sent Yesterday)
                //  - то в списке писем должно быть выделено именно это письмо
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'MS') {
            // если исходящее письмо не отправлено  (кодировка MS - Message Sent) - то должно открыться окно написания нового письма
            return array('result' => 1, 'eventType' => $type);
        }
        
        if ($type == 'D') {
            // определить документ по коду
            $documentTemplateModel = MyDocumentsTemplateModel::model()->byCode($code)->find();
            if (!$documentTemplateModel) return false;
            $templateId = $documentTemplateModel->id;
            
            $document = MyDocumentsModel::model()->byTemplateId($templateId)->bySimulation($simId)->find();
            if (!$document) return false;
            
            return array('result' => 1, 'eventType' => $type, 'id' => $document->id);
        }
        
        if ($type == 'P') {
            $task = Tasks::model()->byCode($code)->find();
            if (!$task) return false;
            // проверим есть ли такая задача у нас в туду
            $todo = Todo::model()->bySimulation($simId)->byTask($task->id)->find();
            if (!$todo) {
                $todo = new Todo();
                $todo->sim_id = $simId;
                $todo->task_id = $task->id;
                $todo->insert();
            }
            
            return array('result' => 1, 'eventType' => $type, 'id' => $task->id);
        }
        
        return $result;
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
            
            
            ###############################
            $gameTime = SimulationService::getGameTime($simulation->id);
            /*$gameTime = SimulationService::getGameTime($simulation->id);
            $toTime = $gameTime + 5*60;
            Logger::debug("try to fine event : {$gameTime} to {$toTime}");
            // проверим а нет ли ближ события
            $event = EventsSamples::model()->nearest($gameTime, $toTime)->find();
            if ($event) {
                Logger::debug("found event : {$event->code}");
            }
            if (!$event) {*/
            ##########################################
            
            // получить ближайшее событие
            Logger::debug("try to find trigger for time $gameTime sim {$simulation->id}");
            $triggers = EventsTriggers::model()->nearest($simulation->id, $gameTime)->findAll();
            //var_dump($triggers); die();
            if (count($triggers) == 0) throw new Exception('Нет ближайших событий', 4);

            $trigger = $triggers[0];  // получаем актуальное событие для заданной симуляции
            Logger::debug("found trigger : {$trigger->event_id}");
            
            // получить диалог
            $event = EventsSamples::model()->findByAttributes(array('id'=>$trigger->event_id));
            if (!$event) throw new Exception('Не могу определить конкретное событие for event '.$trigger->event_id, 5);
            //} //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

            // Убиваем обработанное событие
            $trigger->delete();
            
            Logger::debug("found event : {$event->code}");
            
            $result = $this->_processLinkedEntities($event->code, $simulation->id);
            if ($result) {
                return $this->_sendResponse(200, CJSON::encode($result));
            }

            // выбираем записи из диалогов где code = code, step_number = 1
            //$dialogs = Dialogs::model()->byCodeAndStepNumber($event->code, 1)->findAll();
            
            Logger::debug("get dialogs by code : {$event->code}");
            $dialogs = Dialogs::model()->byCode($event->code)->byStepNumber(1)->findAll();
            //Logger::debug("found dialogs for event : {$event->code} " .var_export);
            
            // Убиваем обработанное событие
            //if (isset($trigger))
            //$trigger->delete();
            
            $data = array();
            foreach($dialogs as $dialog) {
                Logger::debug("check dialog by code : {$dialog->code} next event : {$dialog->next_event_code}");
                
                /*if ($dialog->next_event_code == '-')  continue;
                
                if ($dialog->next_event_code != '' && $dialog->next_event_code != '-') {
                // проверить есть ли событие по такому коду и если есть то создать его
                    $event = EventsSamples::model()->byCode($dialog->next_event_code)->find();
                    if ($event) {
                        $eventsTriggers = new EventsTriggers();
                        $eventsTriggers->sim_id         = $simulation->id;
                        $eventsTriggers->event_id       = $event->id;
                        $eventsTriggers->trigger_time   = $event->trigger_time; 
                        $eventsTriggers->save();
                    }
                }*/
                
                /* old
                // обработка внешних сущностей
                $result = $this->_processLinkedEntities($dialog->next_event_code, $simulation->id);
                if ($result) {
                    return $this->_sendResponse(200, CJSON::encode($result));
                }*/
                
                $data[] = DialogService::dialogToArray($dialog);
            }
            
            if (isset($data[0]['ch_from'])) {
                $characterId = $data[0]['ch_from'];
                //Logger::debug("get character title : $characterId");
                $character = Characters::model()->byId($characterId)->find();
                //Logger::debug("found character : ".var_export($character));
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
