<?php

class LogServiceController extends SimulationBaseController
{
    public function actionSoundSwitcher()
    {
        LogHelper::soundSwitcher(
            $this->getSimulationEntity(),
            Yii::app()->request->getParam('is_play', null),
            Yii::app()->request->getParam('sound_alias', null)
        );
        $this->sendJSON(['result'=>self::STATUS_SUCCESS]);
    }

 }


