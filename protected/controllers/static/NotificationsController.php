<?php

class NotificationsController extends SiteBaseController implements AccountPageControllerInterface
{
    /**
     * @return string
     */
    public function getBaseViewPath()
    {
        return '/static/notifications';
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
        $this->render('notifications_personal', []);
    }

    /**
     *
     */
    public function actionCorporate()
    {
        $this->render('notifications_personal', []);
    }
}