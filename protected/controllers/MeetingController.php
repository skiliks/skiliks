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
}


