<?php

class StatisticsController extends AjaxController
{
    /**
     *
     */
    public function actionCiTests()
    {
        $this->checkUserDeveloper();

        $this->layout = false;

        $this->render('ci_tests');
    }

    /**
     *
     */
    public function actionPhpUnitTests()
    {
        $this->checkUserDeveloper();

        $this->layout = false;

        $this->render('php_unit_tests');
    }

    public function actionSeleniumTests()
    {
        $this->checkUserDeveloper();

        //$this->layout = 'statistics';
        $this->layout = false;
        $this->render('selenium_tests');
    }

    public function actionSeleniumTestsAuth()
    {
        $this->layout = false;
        $this->checkUserDeveloper();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://ci.dev.skiliks.com' . $_GET['params']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic aW5kaWNhdG9yOmluZGljYXRvcg==']);
        curl_exec($ch);
    }

    public function actionZoho500()
    {
        $this->checkUserDeveloper();

        //$this->layout = 'statistics';
        $this->layout = false;
        $this->render('zoho_500');
    }
}