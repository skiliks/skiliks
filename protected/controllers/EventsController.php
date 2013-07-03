<?php

class EventsController extends SimulationBaseController
{

    /**
     * Опрос состояния событий
     */
    public function actionGetState()
    {
        $this->sendJSON(
            EventsManager::getState(
                $this->getSimulationEntity(),
                Yii::app()->request->getParam('logs', null),
                Yii::app()->request->getParam('eventsQueueDepth', 0)
            )
        );
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
        $this->sendJSON(
            EventsManager::startEvent(
                $this->getSimulationEntity(),
                Yii::app()->request->getParam('eventCode'),
                Yii::app()->request->getParam('clearEvents', false),
                Yii::app()->request->getParam('clearAssessment', false),
                Yii::app()->request->getParam('delay', 0),
                Yii::app()->request->getParam('gameTime', null)
            )
        );
    }

    /**
     *
     */
    public function actionWait()
    {
        $this->sendJSON(
            EventsManager::waitEvent(
                $this->getSimulationEntity(),
                Yii::app()->request->getParam('eventCode'),
                Yii::app()->request->getParam('eventTime')
            )
        );
    }
}
        

