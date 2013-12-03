<?php
/*
 * Контроллер статистики для офиса, PHPUnit, Selenium
 */
class StatisticsController extends SiteBaseController
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

    public function actionOrderCount()
    {
        $this->checkUserDeveloper();
        $this->layout = false;
        $this->render('order_count', [
            'total' => Invoice::model()->count(),
            'today' => Invoice::model()->count('created_at > CURDATE()'),
            'server' => Yii::app()->request->serverName
        ]);
    }

    public function actionFeedbackCount()
    {
        $this->checkUserDeveloper();

        //$this->layout = 'statistics';
        $this->layout = false;
        $this->render('feedback_count', [
            'count' => Feedback::model()->count(),
            'count_today' => Feedback::model()->count(" addition >= :addition", ['addition'=>(new DateTime())->format("Y-m-d")])
        ]);
    }
}