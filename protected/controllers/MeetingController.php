<?php

class MeetingController extends SimulationBaseController {
    
    public function actionGetSubjects()
    {
        /** @var Simulation $simulation */
        $simulation = $this->getSimulationEntity();

        // после 18:00game_type
        if ($simulation->game_type->scenario_config->game_end_workday_timestamp < $simulation->getGameTime()) {
            FlagsService::checkFlagsDelay($simulation);
        }

        $this->sendJSON([
            'result' => self::STATUS_SUCCESS,
            'data'   => MeetingService::getList(
                $this->getSimulationEntity()
            )
        ]);
    }

    /**
     * @todo: переименовать в goToMeeting
     */
    public function actionLeave()
    {
        $time = MeetingService::leave(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('id', null)
        );

        $this->sendJSON([
            'result' => self::STATUS_SUCCESS,
            'time' => $time
        ]);
    }
}


