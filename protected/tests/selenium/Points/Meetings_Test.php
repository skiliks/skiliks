<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Эффективное управление встречами (Область обучения №7)
 */
class Meetings_Test extends SeleniumTestHelper
{
//SK2420_4
    public function test_meetings_management()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->run_event('E3.1',"xpath=(//*[contains(text(),'Здравствуйте, Анжела')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Благодарю, польщен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Сколько именно времени  вам нужно и для чего')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А давайте я позвоню вам в первый же день после отпуска')])");

        $this->run_event('ET3.2',"css=li.icon-active.door a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Да возимся еще, надеюсь, к обеду будет')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай по порядку')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну, знаешь, мы ведь в одной упряжке')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Если бы все было так просто')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Чего тут считать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Наверное, ты прав')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Иван,  наши сорок минут прошли')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо. Прямо сейчас в план поставлю и пришлю')])");

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('ET12.4',"xpath=(//*[contains(text(),'Кхе-кхе…')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, доволен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ваша презентация была не единственным его промахом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это наши корпоративные цвета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы вместе с сотрудниками. Они готовили – я проверял.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Правильно я понял, в презентации оставляем все как есть')])");

        $this->run_event('ET13',"css=li.icon-active.door a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, могу я чем-то помочь')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А что именно должно поменяться и почему')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты можешь рассказать подробнее, какие задачи для тебя интересны')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же ты раньше об этом не говорила')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай так договоримся')])");
        sleep(10);

        $this->run_event('ET15',"css=li.icon-active.door a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, прошу прощения')])");
        sleep(7);

        $this->run_event('T6.1',"xpath=(//*[contains(text(),'Валерий Семенович просил')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Среда, 17.00, у вас в коробках, сорок копий')])");

        //странная реплика конечная
        $this->run_event('RS8.1',"xpath=(//*[contains(text(),'Добрый день! Федоров. У меня есть к вам важный вопрос по теме бюджета')])", 'click');
        sleep(7);

        $this->run_event('RVT1',"css=li.icon-active.door a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['deny']);

        sleep(2);
        $this->run_event('MS65');
        sleep(2);
        $this->run_event('MS55');
        sleep(2);

        $this->run_event('RVT1.1',"css=li.icon-active.door a",'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);


        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['meetings7'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['meetings7'],"100");
        $this->close();
    }
}
