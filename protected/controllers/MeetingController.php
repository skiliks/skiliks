<?php

class MeetingController extends SimulationBaseController {
    
    public function actionGetSubjects()
    {
        $this->sendJSON([
            'result' => self::STATUS_SUCCESS,
            'data'   => MeetingService::getList(
                $this->getSimulationEntity()
            )
        ]);
    }

    public function actionLeave()
    {
        $result = MeetingService::leave(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('id', null)
        );

        $this->sendJSON([
            'result' => self::STATUS_SUCCESS,
            'data'   => $result
        ]);
    }
}


