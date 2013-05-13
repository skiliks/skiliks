<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Оптимальный выбор каналов коммуникации (Область обучения №5)
 */
class Flexibility_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function test_flexibility_max()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('E1.2',"xpath=(//*[contains(text(),'Марина, есть срочная работа')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну ладно, придется делать самому')])");

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию для Генерального')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше. Жду в 15.30')])");

        $this->run_event('E1.3.2',"xpath=(//*[contains(text(),'Я тебя для чего тут держу?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, я сам все сделаю, письмо от логистов у меня тоже есть')])");

        $this->run_event('E3',"xpath=(//*[contains(text(),'Ох, Иван, раз такое дело, может, перенесем встречу')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Спасибо тебе, значит, через две недели и увидимся')])");

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        sleep(2);

        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "E8.3");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, прости, Мирон. Сегодня просто сумасшедший день')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Семен, а у тебя наверняка в бюджете статейка есть на непредвиденные расходы')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас вернусь и напишу служебку. Спасибо за информацию! ')])");

        $this->run_event('E9',"xpath=(//*[contains(text(),'Василий, вопрос в чем?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");

        $this->run_event('RS8',"xpath=(//*[contains(text(),'Добрый день, а вы по какому вопросу')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, я отвечаю за итоговую версию бюджета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так в чем же проблема')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понимаю. Давайте на цифрах посмотрим, серьезно ли расходится бюджет')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я попробую')])");
        sleep(7);

        $this->run_event('E3.5',"xpath=(//*[contains(text(),'Здравствуйте, Анжела')])", 'click');
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'Чуть больше года')])");
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'Вероятно, начал бы с команды')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Простите, но у меня сейчас начинается давно запланированная встреча')])");


        $this->assertTrue($this->verify_flag('F14','1'));
        $this->run_event('E12',"xpath=(//*[contains(text(),'Я вас очень прошу')])",'click');
        sleep(5);
        $this->transfer_time(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько')])");
        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])",'click');


        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        sleep(5);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal16'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal16'],"100");
        //sleep(20);
        /*      $this->waitForVisible("id=simulation-points");
                $this->waitForTextPresent('Simulation points');
                $this->checkSimPoints('9.667','-10');
                $this->checkLearningArea('2.56','0.00','0.00','4.55','2.41','15','8.33','10');*/
    }
}
