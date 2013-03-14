<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на тестирование счетчика писем (для SK1471)
 */
class Case_SK1471_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * test_SK1471_Case() тестирует задачу SKILIKS-1471
     *
     * 1. Устанавливаем время на 12:00
     * 2. Проверяем, что количество входящих писем на даный момент = 10
     * 3. Устанавливаем время на 16:00
     * 4. Проверяем, что количество входящих  писем на даный момент = 23
     * 5. Устанавливаем время на 17:50
     * 6. Проверяем, что количество входящих писем на даный момент = 32
     */
    public function test_SK1471_Case() {

        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "12");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "00");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->assertTrue($this->incoming_counter(10));


        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "16");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "00");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->assertTrue($this->incoming_counter(23));


        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "17");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "50");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->assertTrue($this->incoming_counter(32));

        $this->click("css=input.btn.btn-simulation-stop");
    }
}