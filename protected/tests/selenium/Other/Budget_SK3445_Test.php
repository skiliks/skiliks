<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки возможности посчитать сводный бюджет (для SK3445)
 */
class Budget_SK3445_Test extends SeleniumTestHelper
{
    public function test_Budget_SK3445 ()
    {
        $this->start_simulation("Budget_SK3445_Test");

        $this->clearEventQueueBeforeEleven('RST1');
        // получаем бюджет логистики
        $this->run_event('T3.1',"xpath=(//*[contains(text(),'Егор, приветствую')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, но у тебя не больше пяти минут')])");
        // получаем бюджет производства
        $this->run_event('ET9', Yii::app()->params['test_mappings']['icons']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, вопрос в чем?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");
        sleep(30);
        // сохраняем документы
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click("xpath=(//*[contains(text(), 'бюджет логистики')])");
        $this->optimal_click("css=.save-attachment-icon");
        $this->optimal_click("css=.mail-popup-button");
        $this->optimal_click("xpath=(//*[contains(text(), 'новый бюджет по производству')])");
        $this->optimal_click("css=.save-attachment-icon");
        $this->optimal_click("css=.mail-popup-button");

        $this->click(Yii::app()->params['test_mappings']['icons']['close']);

        //начинаем работу с документами
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['documents']);
        sleep(5);
        $this->mouseDown("xpath=//*[@id='Бюджет производства_2013_утв.xls']/div[1]");
        $this->click("xpath=//*[@id='Бюджет производства_2013_утв.xls']/div[1]");
        $this->doubleClick("xpath=//*[@id='Бюджет производства_2013_утв.xls']/div[1]");
        sleep(5);
        // проверяем, что есть ячейка В7 - значит документ загружен
        $this->assertTrue($this->isVisible("css=#cell_B7"));
        $this->mouseMoveAt("css=#cell_B7");
        $this->mouseDownAt("css=#cell_B7");
        $this->keyDownNative('16');
        $this->mouseMoveAt("css=#cell_M14");
        $this->click("css=#cell_M14");
        $this->keyUpNative('16');
        // копируем данные с бюджета производства
        $this->mouseMoveAt("xpath=//div[1]/ul[1]/li[3]/img");
        $this->click("xpath=//div[1]/ul[1]/li[3]/img");
        // закрываем бюджет производтсва
        $this->mouseMoveAt(Yii::app()->params['test_mappings']['icons']['close']);
        $this->click(Yii::app()->params['test_mappings']['icons']['close']);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['documents']);
        sleep(5);
        // открываем СБ
        $this->mouseDown("xpath=//*[@id='Сводный бюджет_2014_план.xls']/div[1]");
        $this->click("xpath=//*[@id='Сводный бюджет_2014_план.xls']/div[1]");
        $this->doubleClick("xpath=//*[@id='Сводный бюджет_2014_план.xls']/div[1]");
        sleep(5);

        $this->assertTrue($this->isVisible("css=#cell_B7"));
        // идем на вкладку производство
        $this->mouseDown("xpath=(//*[contains(text(), 'производство')])");
        $this->optimal_click("xpath=(//*[contains(text(), 'производство')])");

        $this->mouseMoveAt("css=#cell_B7");
        $this->mouseDownAt("css=#cell_B7");
        // вставляем данные
        $this->mouseMoveAt("xpath=//div[1]/ul[1]/li[4]/img");
        $this->click("xpath=//div[1]/ul[1]/li[4]/img");
        sleep(10);

        $this->simulation_stop();
    }
}