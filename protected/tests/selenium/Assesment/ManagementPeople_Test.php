<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Управление людьми (Область обучения №2)
 */
class ManagementPeople_Test extends SeleniumTestHelper
{

    public function testManagementPeople_Positive()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->clearEventQueueBeforeEleven('RST1');

        // Delegation
        $this->optimal_click("link=F36");
        $this->optimal_click("link=F39");
        $this->optimal_click("link=F14");

        $this->run_event('MS28');
        sleep(2);
        $this->run_event('M41');
        sleep(2);
        $this->run_event('M47');
        sleep(2);
        $this->run_event('M72');
        sleep(2);
        $this->run_event('MS39');
        sleep(2);
        $this->run_event('MS42');
        sleep(2);
        $this->run_event('MS46');
        sleep(2);
        $this->run_event('MS67');
        sleep(2);

        $this->run_event('E1',"xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет. Сейчас же приступаю к доработке. ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь.')])");
        sleep(5);

        $this->run_event('E8.3');
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно читал. Хорошее письмо, обстоятельное!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да у Денежной снега зимой не выпросишь, а тут деньги вне бюджета! Что же делать?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я все понял. Запланирую подготовку служебки сегодня-завтра. Спасибо!')])");

        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, буду в 18.00')])");

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов по выгрузке данных. Трудякин просил сегодня! Ты его сделал?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В таких случаях надо сразу же спрашивать у меня или у заказчика. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом я должен стоять в копии! ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо,  сейчас поговорю с ним и уточню задание! Но впредь учти – детализация задачи – часть твоей работы!')])");

        $this->run_event('RS2',"xpath=(//*[contains(text(),'Доброе утро, Егор. Не совсем – я бюджетом занят.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Какой план? Я бюджетом занят!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, Егор! До отпуска времени у меня нет. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сегодня вечером, после шести!')])");

        $this->run_event('M65');
        sleep(2);
        $this->run_event('MS48');
        sleep(2);
        $this->run_event('MS65');
        sleep(2);
        $this->run_event('MS28');
        sleep(2);

        $this->run_event('E2',"xpath=(//*[contains(text(),'Конечно, Валерий Семенович! Буду у Вас в 16.00 с готовой презентаций.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, у меня в графике уже выделено время на проверку')])");

        $this->optimal_click("link=F38_1");
        $this->optimal_click("link=F38_2");

        $this->run_event('T7.3');
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, отложи все дела и сделай срочно. Думаю,')])");

        $this->run_event('E12.4',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Добрый день, Валерий Семенович!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, доволен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович, я вам гарантирую')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это наши корпоративные цвета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы вместе с сотрудниками.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошего вам выступления,')])");

        // Feedback
        $this->run_event('E2.10',"xpath=(//*[contains(text(),'Раз ты уверена, что задача простая и времени хватит – продолжай работать. Полагаюсь на тебя, жду презентацию вовремя.')])",'click');

        $this->run_event('E13',"xpath=(//*[contains(text(),'Марина, могу я чем-то помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'А что именно должно поменяться и почему?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение, но мне важно знать, что именно на него повлияло.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне, в компании или типе задач?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты можешь рассказать подробнее, какие задачи для тебя интересны? Что вызывает желание работать?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, что мы об этом поговорили, думаю, что у нас есть шанс все поправить!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ты дашь мне второй шанс.')])");
        sleep(5);

        $this->run_event('M10');
        sleep(2);
        $this->run_event('MS37');
        sleep(2);

        // Resources

        $this->assertTrue($this->verify_flag('F37','1'));

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, привет! Что там с презентацией для Генерального? ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше. Жду в 15.30')])");

        $this->optimal_click("link=F35");
        $this->run_event('T5.2',"xpath=(//*[contains(text(),'Марина, я по поводу презентации. Спасибо, что прислала вовремя, как договаривались. Работа полностью соответствует требованиям. Мне и корректировать нечего.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Я дам тебе знать, что думает Босс, после встречи с ним. ')])");

        $this->run_event('T6.2',"xpath=(//*[contains(text(),'Марина, запиши, пожалуйста, важную информацию по презентации.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет-нет. Ее надо распечатать. Запиши, пожалуйста.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Главное, чтобы хватило. Сорок копий.')])");

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "12");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "38");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Ты его сделал?')])",'click');

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['management3'],"100");
        $this->close();
    }
}