<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Проверка наличия документов-вложений при форварде письма
 */
class ForwardEmailAttributes_SK5188_Test extends SeleniumTestHelper
{
    public function test_ForwardEmailAttributes_SK5188()
    {
        $this->start_simulation("ChangeMailThemes_SK1253_Test");
        sleep(5);

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");
        sleep(3);

        $this->run_event('T7.2',"xpath=(//*[contains(text(),'Егор, ты хотел сегодня получить выгрузку данных! Она тебе все еще нужна?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Но что именно ты ждешь? Я об этом ничего не знаю, исполнитель – Трутнев – тоже. Он тебе два дня назад письмо отправил с уточнениями, ответа не получил!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Именно об этом я и говорю. Трутнев два дня назад написал тебе письмо с уточнениями, но ты не ответил. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Когда тебе нужны данные?')])");
        sleep(30);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']);
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'форма по задаче от логистики, срочно!')])");
        $this->assertTextPresent('Форма выгрузки данных для логистов_чистая.xls');

        $this->optimal_click(Yii::app()->params['test_mappings']['mail_main']['forward_email']);
        // проверка правильная ли тема у форварда и есть ли документ
        $this->waitForTextPresent('Fwd: форма по задаче от логистики, срочно!');
        $this->waitForTextPresent('Форма выгрузки данных для логистов_чистая.xls');
        // проверка можно ли добавить адресата
        $this->addRecipient(Yii::app()->params['test_mappings']['mail_contacts']['trutnev']);

        $this->simulation_stop();
    }
}