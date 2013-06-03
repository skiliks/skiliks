<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Результативность (проверка всех формул без 4, 31-40 = excel)
 */
class Productivity_points_Test extends SeleniumTestHelper
{
    public function testProductivity()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        $this->run_event('T2',"xpath=(//*[contains(text(),'Иван, привет! Это Федоров. У нас с тобой была договоренность о встрече. Сегодня около часа-двух дня. Помнишь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Послушай, Иван, а мы можем встретиться, когда я вернусь из отпуска? Через две недели? У меня два дня осталось, приходится концентрироваться на срочном!')])");
        sleep(20);

        $this->run_event('T3.1',"xpath=(//*[contains(text(),'Егор, приветствую! Вынужден напомнить про ваш бюджет. У меня  3 часа для завершения сводного. Жду только вас.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, но у тебя не больше пяти минут!  Я на тебя полагаюсь и жду! ')])");
        sleep(2);

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов по выгрузке данных. Трудякин просил сегодня! Ты его сделал?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В таких случаях надо сразу же спрашивать у меня или у заказчика. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом я должен стоять в копии! ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо,  сейчас поговорю с ним и уточню задание! Но впредь учти – детализация задачи – часть твоей работы! ')])");
        sleep(2);

        $this->run_event('T7.2',"xpath=(//*[contains(text(),'Егор, ты хотел сегодня получить выгрузку данных! Она тебе все еще нужна?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Но что именно ты ждешь? Я об этом ничего не знаю, исполнитель – Трутнев – тоже. Он тебе два дня назад письмо отправил с уточнениями, ответа не получил!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Именно об этом я и говорю. Трутнев два дня назад написал тебе письмо с уточнениями, но ты не ответил. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Когда тебе нужны данные?')])");
        sleep(2);

        $this->run_event('T7.3',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным. Им нужно заполнить данными определенную форму за прошедшие девять месяцев. Она у тебя в почте.  Если будут вопросы – звони не откладывая.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, отложи все дела и сделай срочно. Думаю, двух часов тебе хватит. Перед отправкой перешли мне для проверки. ')])");
        sleep(2);

        $this->optimal_click("link=F38_3");
        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Ты его сделал?')])",'click');
        sleep(2);

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        sleep(5);
        $this->run_event('MS45');
        sleep(2);
        $this->run_event('E10',"xpath=(//*[contains(text(),'Хорошо, сейчас найду и перешлю. ')])",'click');
        sleep(2);
        $this->run_event('MS28');
        sleep(2);
        $this->run_event('RS2',"xpath=(//*[contains(text(),'Приветствую, Егор!  У тебя что-то срочное?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я начну работу по проекту только после отпуска. Я перешлю тебе свой квартальный план, а завтра обудим, хорошо?')])");
        sleep(2);

        $this->run_event('MS69');
        sleep(2);
        $this->run_event('MS35');
        sleep(2);
        $this->run_event('MS20');
        sleep(2);

        $this->run_event('E9',"xpath=(//*[contains(text(),'Василий, вопрос в чем?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");
        sleep(2);

        $this->run_event('MS36');
        sleep(2);
        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию для Генерального! Босс сам звонил и интересовался!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше. Жду в 15.30')])");
        sleep(5);

        // formula 5
        $this->run_event('M10');
        sleep(20);
        $this->run_event('MS83');
        sleep(5);

        $this->optimal_click("link=F36");
        $this->optimal_click("link=F37");

        //$this->run_event('MS37');
        //sleep(2);
        $this->run_event('T6.1',"xpath=(//*[contains(text(),'Валерий Семенович просил уточнить сколько копий презентаций и к какому числу необходимо сделать.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Среда, 17.00, у вас в коробках, сорок копий. Спасибо!')])");
        sleep(5);
        $this->run_event('MS39');
        sleep(10);
        $this->run_event('RS8.1',"xpath=(//*[contains(text(),'Добрый день! Федоров. У меня есть к вам важный вопрос по теме бюджета. Давайте встретимся завтра. Минут на тридцать.')])",'click');
        sleep(10);

        // formula 18
        sleep(10);
        $this->run_event('MS48');
        sleep(5);
        $this->run_event('MS55');
        sleep(5);

        $this->run_event('M47');
        $this->run_event('M72');
        $this->run_event('M1');
        $this->run_event('M2');
        $this->run_event('M76');
        $this->run_event('M71');
        $this->run_event('M73');
        $this->run_event('M74');
        $this->run_event('M75');
        $this->run_event('M65');

        $this->run_event('MS46');
        sleep(2);
        $this->run_event('MS67');
        sleep(2);
        $this->run_event('MS40');
        sleep(2);
        $this->run_event('MS52');
        sleep(2);
        $this->run_event('MS61');
        sleep(2);
        $this->run_event('MS63');
        sleep(2);
        $this->run_event('MS62');
        sleep(2);
        $this->run_event('MS64');
        sleep(2);
        $this->run_event('MS60');
        sleep(2);
        $this->run_event('MS65');
        sleep(2);
        $this->run_event('MS53');
        sleep(2);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("xpath=//tr[contains(@class, 'performance-aggregated-0')]/td[3]");
        $this->assertText("xpath=//tr[contains(@class, 'performance-aggregated-0')]/td[3]","31%");
        $this->assertText("xpath=//tr[contains(@class, 'performance-aggregated-1')]/td[3]","80%");
        $this->assertText("xpath=//tr[contains(@class, 'performance-aggregated-2')]/td[3]","100%");
        $this->assertText("xpath=//tr[contains(@class, 'performance-aggregated-2_min')]/td[3]","100%");
        $this->close();
    }
}
