<?php

class StatisticsController extends AjaxController
{
    /**
     *
     */
    public function actionIndex()
    {
        $this->checkUserDeveloper();

        $this->layout = 'statistics';

        $this->render('index');
    }

    /**
     *
     */
    public function actionPhpUnitTests()
    {
        $this->checkUserDeveloper();

        $this->layout = 'statistics';

        $this->render('php_unit_tests');
    }
}