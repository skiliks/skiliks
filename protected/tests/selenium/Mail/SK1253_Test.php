<?php
/**
 * Тест пишет новое письмо двум адресатам, потом удаляет первого, и проверяет
 * корректность соотвествия оставшегося адресата и доступных тем.
 */
class SeleniumMailTest extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testSK1253() {
        $this->markTestIncomplete();
        $this->start_simulation();
        //маппинги Трудякина и Крутько в выпадающем списке адресатов
        $trudyakin = Yii::app()->params['test_mappings']['mail_contacts']['trudyakin'];
        $krutko = Yii::app()->params['test_mappings']['mail_contacts']['krutko'];

        $this->click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->click(Yii::app()->params['test_mappings']['mail']['new_letter']);
        $this->click(Yii::app()->params['test_mappings']['mail']['to_whom']);
        $this->waitForElementPresent($trudyakin);
        $this->mouseOver($trudyakin);
        $this->click($trudyakin);
	    $this->click(Yii::app()->params['test_mappings']['mail']['add_recipient']);
        $this->mouseOver($krutko);
        $this->click($krutko);
        $this->select("css=select.origin", "Срочно жду бюджет логистики");
        $this->click(Yii::app()->params['test_mappings']['mail']['del_recipient']);
        $this->click(Yii::app()->params['test_mappings']['mail']['button_to_continue']);
        $this->waitForVisible("css=select.origin");
        $this->select("css=select.origin", "Сводный бюджет: файл");

        $this->assertFalse($this->isTextPresent('Срочно жду бюджет логистики'));

    }
}

