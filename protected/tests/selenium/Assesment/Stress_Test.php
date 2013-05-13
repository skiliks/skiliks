<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Стрессоустойчивость = 100%
 */
class Stress_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testStress()
    {
        // $this->markTestIncomplete();
        $this->start_simulation();

        // 3
        $this->run_event('T3.1',"xpath=(//*[contains(text(),'Егор, приветствую! Вынужден напомнить про ваш бюджет. У меня  3 часа для завершения сводного. Жду только вас.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, но у тебя не больше пяти минут!  Я на тебя полагаюсь и жду! ')])");

        // 1
        $this->run_event('T2',"xpath=(//*[contains(text(),'Иван, привет! Это Федоров. У нас с тобой была договоренность о встрече. Сегодня около часа-двух дня. Помнишь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Послушай, Иван, а мы можем встретиться, когда я вернусь из отпуска? Через две недели? У меня два дня осталось, приходится концентрироваться на срочном!')])");
        sleep(20);

        // 9
        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов по выгрузке данных. Трудякин просил сегодня! Ты его сделал?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В таких случаях надо сразу же спрашивать у меня или у заказчика. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом я должен стоять в копии! ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо,  сейчас поговорю с ним и уточню задание! Но впредь учти – детализация задачи – часть твоей работы! ')])");

        // 10
        $this->run_event('T7.2',"xpath=(//*[contains(text(),'Егор, ты хотел сегодня получить выгрузку данных! Она тебе все еще нужна?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Но что именно ты ждешь? Я об этом ничего не знаю, исполнитель – Трутнев – тоже. Он тебе два дня назад письмо отправил с уточнениями, ответа не получил!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Именно об этом я и говорю. Трутнев два дня назад написал тебе письмо с уточнениями, но ты не ответил. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Когда тебе нужны данные?')])");

        // 13
        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Ты его сделал?')])",'click');

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        sleep(5);

        // 14
        $this->run_event('MS45');
        sleep(2);

        // 7
        $this->run_event('MS83');
        sleep(2);

        // 4
        $this->run_event('MS35');
        sleep(2);

        // 15
        $this->run_event('MS20');

        // 5
        $this->run_event('E9',"xpath=(//*[contains(text(),'Василий, вопрос в чем?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");

        // 6
        $this->run_event('MS36');
        sleep(2);

        // 17
        $this->run_event('RS8',"xpath=(//*[contains(text(),'Добрый день, а вы по какому вопросу')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, я отвечаю за итоговую версию бюджета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так в чем же проблема')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понимаю. Давайте на цифрах посмотрим, серьезно ли расходится бюджет')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я попробую')])");
        sleep(7);

        // 2
        $this->run_event('E1',"xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет. Сейчас же приступаю к доработке. ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь.')])");
        sleep(5);

        // 12
        $this->run_event('M41');
        sleep(2);
        $this->run_event('M47');
        sleep(2);
        $this->run_event('M72');
        sleep(2);
        $this->run_event('MS42');
        sleep(2);

        // 19
        $this->run_event('E13',"xpath=(//*[contains(text(),'Марина, могу я чем-то помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'А что именно должно поменяться и почему?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение, но мне важно знать, что именно на него повлияло.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне, в компании или типе задач?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты можешь рассказать подробнее, какие задачи для тебя интересны? Что вызывает желание работать?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, что мы об этом поговорили, думаю, что у нас есть шанс все поправить!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ты дашь мне второй шанс.')])");
        sleep(5);

        //18
        $this->run_event('E2',"xpath=(//*[contains(text(),'Конечно, Валерий Семенович! Буду у Вас в 16.00 с готовой презентаций.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, у меня в графике уже выделено время на проверку')])");


        // 11
        $this->run_event('T7.3');
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, отложи все дела и сделай срочно. Думаю,')])");

        // 8
        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Добрый день, Валерий Семенович!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, доволен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович, я вам гарантирую')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это наши корпоративные цвета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы вместе с сотрудниками.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошего вам выступления,')])");

        // 16
        $this->run_event('RS6', "xpath=(//*[contains(text(),'давайте я вам перешлю этот показатель')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Через пять минут данные будут у вас')])");
        sleep(5);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(60);

    }
}
