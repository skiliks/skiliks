<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 11:28 PM
 * To change this template use File | Settings | File Templates.
 */

class NotificationsController extends AjaxController implements AccountPageControllerInterface
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