<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 11:21 PM
 * To change this template use File | Settings | File Templates.
 */

class StatisticController extends AjaxController implements AccountPageControllerInterface
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
}