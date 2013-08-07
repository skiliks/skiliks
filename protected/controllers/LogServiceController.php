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

    public function actionAddInviteLog()
    {
        $inviteId = Yii::app()->request->getParam('inviteId');
        $action   = Yii::app()->request->getParam('action');

        $invite = Invite::model()->findByPk($inviteId);

        InviteService::logAboutInviteStatus($invite, $action);

        $this->sendJSON(['result'=>self::STATUS_SUCCESS]);
    }
}


