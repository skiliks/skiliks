<?php



/**
 * Движек событий
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsController extends AjaxController{
    
    
    /**
     * Опрос состояния событий
     */
    public function actionGetState() {
        $sid = Yii::app()->request->getParam('sid', false);  
        if (!$sid) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не задан sid',
                'code' => 1
            )));
        }
        
        // получить uid
        $uid = SessionHelper::getUidBySid($sid);
        if (!$uid) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить пользователя',
                'code' => 2
            )));
        }
        
        // получить симуляцию по uid
        $simulation = Simulations::model()->byUid($uid)->find();
        if (!$simulation) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить симуляцию',
                'code' => 3
            )));
        }
        
        // получить ближайшее событие
        $triggers = EventsTriggers::model()->nearest($simulation->id)->findAll();
        if (count($triggers) == 0) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Нет ближайших событий',
                'code' => 4
            )));
        }
        $trigger = $triggers[0];
        
        // получить диалог
        $eventSample = EventsSamples::model()->findByAttributes(array('id'=>$trigger->event_id));
        if (!$eventSample) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить конкретное событие for event '.$trigger->event_id,
                'code' => 5
            )));
        }
        
        
        
        // выбираем code из events_samples
        $code = $eventSample->code;
        
        // выбираем записи из диалогов где code =code, step_number = 1
        $dialogs = Dialogs::model()->byCodeAndStepNumber($code, 1)->findAll();
        
        $data = array();
        foreach($dialogs as $dialog) {
            $data[] = DialogService::dialogToArray($dialog);
        }
        
        // Убиваем обработанное событие
        $trigger->delete();
        
        return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data)));
        ##########################################3
        # OLD CODE
        ###########################################
        $dialogId = $eventSample->dialog_id;
        if (!$dialogId) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить диалог',
                'code' => 6
            )));
        }
        
        // Удалить полученное событие
        $trigger->delete();
        
        // если диалог то загружаем диалог
        try {
            $dialog = DialogService::get($dialogId);
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
        
        // запоминаем текущее событие
        $eventsStates = EventsStates::model()->findByAttributes(array(
            'sim_id' => $simulation->id,
            'event_id' => $eventSample->id
        ));
        if ($eventsStates) $eventsStates->delete();
        
        $eventsStates = new EventsStates();
        $eventsStates->sim_id = $simulation->id;
        $eventsStates->event_id = $eventSample->id;
        $eventsStates->cur_dialog_id = $dialogId;
        $eventsStates->result = 0;
        $eventsStates->insert();
        
        
        $data = array();
        $data[] = DialogService::dialogToArray($dialog);
        
        // загрузить те, где branch = next_branch
        $dialogs = Dialogs::model()->byBranch($dialog->next_branch)->findAll();
        foreach($dialogs as $dialog) {
            $data[] = DialogService::dialogToArray($dialog);
        }
        
        $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data)));
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
        $id = Yii::app()->request->getParam('id', false);  
        $delay = Yii::app()->request->getParam('delay', false);  
        
        try {
            if (!$sid) throw new Exception('Не задан сид');
            
            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception('Не могу определить пользователя');
            
            $simulation = Simulations::model()->byUid($uid)->find();
            if (!$simulation) throw new Exception('Не могу определить симуляцию');
            
            // Добавляем событие
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id = $simulation->id;
            $eventsTriggers->event_id = $id;
            $eventsTriggers->trigger_time = time() + ($delay/4);
            $eventsTriggers->insert();
            
            return $this->_sendResponse(200, CJSON::encode(array('result' => 1)));
            
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0, 'message' => $exc->getMessage()
            )));
        }
    }
}

?>
