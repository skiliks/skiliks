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
                'message' => 'Не задан sid'
            )));
        }
        
        // получить uid
        $uid = SessionHelper::getUidBySid($sid);
        if (!$uid) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить пользователя'
            )));
        }
        
        // получить симуляцию по uid
        $simulation = Simulations::model()->findByAttributes(array('user_id' => $uid));
        if (!$simulation) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить симуляцию'
            )));
        }
        
        // получить ближайшее событие
        $events = EventsTriggers::model()->nearest($simulation->id)->findAll();
        if (count($events) == 0) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Нет ближайших событий'
            )));
        }
        $event = $events[0];
        
        // получить диалог
        $eventSample = EventsSamples::model()->findByAttributes(array('id'=>$event->id));
        if (!$eventSample) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить конкретное событие'
            )));
        }
        $dialogId = $eventSample->dialog_id;
        if (!$dialogId) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу определить диалог'
            )));
        }
        
        // Удалить полученное событие
        
        // если диалог то загружаем диалог
        $dialog = Dialogs::model()->findByAttributes(array('id'=>$dialogId));
        if (!$dialog) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => 'Не могу загрузить модель диалога'
            )));
        }
        
        $data = array();
        $data[] = array(
            'id' => $dialog->id,
            'ch_from' => $dialog->ch_from,
            'ch_from_state' => $dialog->ch_from_state,
            'ch_to' => $dialog->ch_to,
            'ch_to_state' => $dialog->ch_to_state,
            'dialog_subtype' => $dialog->dialog_subtype,
            'text' => $dialog->text
        );
        
        // загрузить те, где branch = next_branch
        $dialogs = Dialogs::model()->findAllByAttributes(array('branch_id' => $dialog->next_branch));
        foreach($dialogs as $dialog) {
            $data[] = array(
                'id' => $dialog->id,
                'ch_from' => $dialog->ch_from,
                'ch_from_state' => $dialog->ch_from_state,
                'ch_to' => $dialog->ch_to,
                'ch_to_state' => $dialog->ch_to_state,
                'dialog_subtype' => $dialog->dialog_subtype,
                'text' => $dialog->text
            );
        }
        
        $this->_sendResponse(200, CJSON::encode(array(
                'result' => 1,
                'data' => $data
            )));
    }
}

?>
