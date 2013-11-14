<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы корпоративного кабинета - добавление вакансии, фидбек, ссыди, сортировка в таблице, удаление приглашений(для SK3366)
 */
class Corporate_Dashboard_SK3366_Test extends SeleniumTestHelper
{
    /**
     * test_Corporate_Dashboard_SK3366() тестирует задачу SKILIKS-3366
     */
    public function test_Corporate_Dashboard_SK3366()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'selenium.engine@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);

        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['username'])=="seleniumEngine");


    }
}