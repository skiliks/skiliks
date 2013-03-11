<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест пишет новое письмо двум адресатам, потом удаляет первого и проверяет
 * корректность соотвествия оставшегося адресата и доступных тем.
 */
class SK1253_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    /**
     * Тестирует задачу SKILIKS-1253.
     *
     * 1. Пишет письмо Трудякину
     * 2. Делает что-то еще
     * 3. bla bla bla
     */
    public function testSK1253()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->mouseOver(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail_contacts']['trudyakin']);
	    $this->optimal_click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        $this->mouseOver("//ul[contains(@class,'ui-autocomplete')]/li[15]/a");
        $this->optimal_click("//ul[contains(@class,'ui-autocomplete')]/li[15]/a");
        $this->waitForVisible("css=select.origin");
        $this->select("css=select.origin", "Срочно жду бюджет логистики");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['del_recipient']);
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['button_to_continue']);
        $this->waitForVisible("css=select.origin");
        $this->select("css=select.origin", "Сводный бюджет: файл");
        $this->assertFalse($this->isTextPresent('Срочно жду бюджет логистики'));
    }
}
/**
 * @}
 */