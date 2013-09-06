<?php

class StatisticController extends SiteBaseController implements AccountPageControllerInterface
{
    /**
     * @return string
     */
    public function getBaseViewPath()
    {
        return '/static/statistic';
    }

    /**
     *
     */
    public function actionIndex()
    {
        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionPersonal()
    {
        $this->render('statistic_personal', []);
    }

    /**
     *
     */
    public function actionCorporate()
    {
        $this->render('statistic_personal', []);
    }

    public function actionAddInviteLog()
    {
        $inviteId = Yii::app()->request->getParam('inviteId');
        $action   = Yii::app()->request->getParam('action');

        $invite = Invite::model()->findByPk($inviteId);

        InviteService::logAboutInviteStatus($invite, $action);

        $this->sendJSON(['result' => 1]);
    }


}