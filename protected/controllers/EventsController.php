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
                $result = array('result' => 1, 'data' => $task, 'eventType' => 2);
                return $this->_sendResponse(200, CJSON::encode($result));
            }
            ###################
            
            // получить ближайшее событие
            $triggers = EventsTriggers::model()->nearest($simulation->id)->findAll();
            if (count($triggers) == 0) throw new Exception('Нет ближайших событий', 4);

            $trigger = $triggers[0];  // получаем актуальное событие для заданной симуляции

            // получить диалог
            $event = EventsSamples::model()->findByAttributes(array('id'=>$trigger->event_id));
            if (!$event) throw new Exception('Не могу определить конкретное событие for event '.$trigger->event_id, 5);

            Logger::debug("get event : {$event->code}", 'logs/events.log');

            // выбираем записи из диалогов где code = code, step_number = 1
            $dialogs = Dialogs::model()->byCodeAndStepNumber($event->code, 1)->findAll();

            $data = array();
            foreach($dialogs as $dialog) {
                $data[] = DialogService::dialogToArray($dialog);
            }

            // Убиваем обработанное событие
            $trigger->delete();

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
        $delay = Yii::app()->request->getParam('delay', false);  
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
            
            $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId(
                    $simulation->id, $event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = time() + ($delay/4);
                $eventsTriggers->save(); // обновляем существующее событие в очереди
            }
            else {
                // Добавляем событие
                $eventsTriggers = new EventsTriggers();
                $eventsTriggers->sim_id = $simulation->id;
                $eventsTriggers->event_id = $event->id;
                $eventsTriggers->trigger_time = time() + ($delay/4);
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
