<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки возможности посчитать сводный бюджет и набрать 55 баллов по результативности (связанной с подсчетом сводного бюджета) (для SK3445))
 */
class Budget_SK3445_Test extends SeleniumTestHelper
{
    public function test_Budget_SK3445 ()
    {
        $this->start_simulation();

        /*$this->clearEventQueueBeforeEleven('RST1');

        $this->run_event('T3.1',"xpath=(//*[contains(text(),'Егор, приветствую')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, но у тебя не больше пяти минут')])");

        $this->run_event('ET9', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, вопрос в чем?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");
        sleep(30);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->optimal_click("xpath=(//*[contains(text(), 'бюджет логистики')])");
        $this->optimal_click("css=.save-attachment-icon");
        $this->optimal_click("css=.mail-popup-button");
        $this->optimal_click("xpath=(//*[contains(text(), 'новый бюджет по производству')])");
        $this->optimal_click("css=.save-attachment-icon");
        $this->optimal_click("css=.mail-popup-button");
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['documents']);
        sleep(5);

        //начинаем работу с документами
        $this->doubleClick("xpath=//*[@id='Бюджет производства_2013_утв.xls']/div[1]/div");
        sleep(5);
        $this->waitForElementPresent("css=#cell_B7");
        $this->optimal_click("css=#cell_B7");
        $this->keyDownNative("16");
        $this->optimal_click("css=#cell_M14");

        /*$this->optimal_click();

        $this->doubleClick("xpath=//*[@id='Бюджет логистики_2014_план.xls']/div[2]");

        $this->doubleClick("xpath=//*[@id='Бюджет логистики_2014_план.xls']/div[2]");

        */
        $this->simulation_stop();
    }
}