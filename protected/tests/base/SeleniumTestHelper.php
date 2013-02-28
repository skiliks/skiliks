<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vad
 * Date: 27.02.13
 * Time: 17:07
 * To change this template use File | Settings | File Templates.
 */
class SeleniumTestHelper extends CWebTestCase
{
    public function start_simulation()
    {
        $this->deleteAllVisibleCookies();
        $this->open('/site/');
        //$this->setSpeed("1000");
        $this->waitForVisible('id=login');
        $this->type("id=login", "vad");
        $this->type("id=pass", "123");
        $this->click("css=input.btn.btn-primary");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=//input[@value='Начать симуляцию developer']")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->click("xpath=//input[@value='Начать симуляцию developer']");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=addTriggerSelect")) break;
            } catch (Exception $e) {}
            sleep(1);
        }
    }

    public function run_event($event)
    {
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "$event");
        $this->click(Yii::app()->params['test_mappings']['dev']['event_create']);
    }

    // звонок по телефону, когда телефон не активен (не движется)
    public function call_phone ($whom, $theme)
    {
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->mouseOver(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->click(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->waitForElementPresent($whom);
        $this->mouseOver($whom);
        $this->click($whom);
        $this->waitForElementPresent($theme);
        $this->mouseOver($theme);
        $this->click($theme);
    }

    // ответить на входящий звонок, когда телефон активен (мигает)
    public function reply_call ()
    {
        sleep(5);
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
    }

    // не ответить на входящий звонок, когда телефон активен (мигает)
    public function no_reply_call ()
    {
        sleep(5);
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['no_reply']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        sleep(2);
    }

    // создание письма, когда мейл-клиент активен (мигает)
    public function write_mail_active()
    {
        sleep(2);
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['icons']['phone']);
        sleep(2);
        $this->waitForVisible(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
        $this->click(Yii::app()->params['test_mappings']['phone']['reply']);
        sleep(2);
    }
    /*
    // проверка значения флага
    // flag_numb - локатор к ячейке с проверяемым значением флага в таблице флагов в дев-режиме
    // flag_value - 1 или 0 (значения флагов)
    public function assert_flags($flag_numb, $flag_value)
    {
        $this->assertText(flag_numb,flag_value);
    }
    */
}

