<?php

class EventsController extends SimulationBaseController
{

    /**
     * Опрос состояния событий
     */
    public function actionGetState()
    {
        $result = EventsManager::getState(
                    $this->getSimulationEntity(),
                    Yii::app()->request->getParam('logs', null),
                    Yii::app()->request->getParam('eventsQueueDepth', 0)
                  );
        $this->sendJSON($result);
    }

    public function actionSwitchFlag()
    {
        $simulation = $this->getSimulationEntity();
        $flagName = Yii::app()->request->getParam('flagName', null);

        $json = [
            'result' => FlagsService::switchFlag($simulation, $flagName),
            'flags' => FlagsService::getFlagsStateForJs($simulation)
        ];

        $this->sendJSON($json);
    }

    /**
     * Принудительный старт заданного события
     */
    public function actionStart()
    {
        $result = EventsManager::startEvent(
                    $this->getSimulationEntity(),
                    Yii::app()->request->getParam('eventCode'),
                    Yii::app()->request->getParam('clearEvents', false),
                    Yii::app()->request->getParam('clearAssessment', false),
                    Yii::app()->request->getParam('delay', 0),
                    Yii::app()->request->getParam('gameTime', null)
                );
        $this->sendJSON($result);
    }

    /**
     *
     */
    public function actionWait()
    {
        $result = EventsManager::waitEvent(
                    $this->getSimulationEntity(),
                    Yii::app()->request->getParam('eventCode'),
                    Yii::app()->request->getParam('eventTime')
        );
        $this->sendJSON($result);
    }
}
        

