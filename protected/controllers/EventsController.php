<?php

/**
 * Движек событий
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsController extends AjaxController {

    /**
     * Опрос состояния событий
     */
    public function actionGetState() {
        $event = new EventsManager();
        $json = $event->getState($this->getSimulationEntity());
        $this->sendJSON($json);
        
    }
    
    
    
    /**
     * Возврат списка доступных событий в системе
     */
    public function actionGetList() {
        
        $event = new EventsManager();
        $json = $event->getList();
        $this->sendJSON($json);
        
    }
    
    /**
     * Принудительный старт заданного события
     */
    public function actionStart() {
        
        $manager = new EventsManager();
        $json = $manager->startEvent(
            $this->getSimulationId(),
            Yii::app()->request->getParam('eventCode', false),
            (int)Yii::app()->request->getParam('delay', false),    
            Yii::app()->request->getParam('clearEvents', false),
            Yii::app()->request->getParam('clearAssessment', false)    
        );
        $this->sendJSON($json);
    }
}
        

