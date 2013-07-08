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
}