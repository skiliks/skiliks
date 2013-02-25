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
        $json = $event->getState(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('logs', null)
        );
        $this->sendJSON($json);
        
    }

    public function actionSwitchFlag() {
        $simulation = $this->getSimulationEntity();
        $flagName = Yii::app()->request->getParam('flagName', null);

        $json = [
            'result' => FlagsService::switchFlag($simulation, $flagName),
            'flags'  => FlagsService::getFlagsStateForJs($simulation)
        ];

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
            Yii::app()->request->getParam('clearEvents', false),
            Yii::app()->request->getParam('clearAssessment', false),
            Yii::app()->request->getParam('delay', 0)        );
        $this->sendJSON($json);
    }

    public function actionWait() {
        $manager = new EventsManager();
        $json = $manager->waitEvent(
            $this->getSimulationId(),
            Yii::app()->request->getParam('eventCode', false),
            Yii::app()->request->getParam('eventTime', false)
        );
        $this->sendJSON($json);
    }
}
        

