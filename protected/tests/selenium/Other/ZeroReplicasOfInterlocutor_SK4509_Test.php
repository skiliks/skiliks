<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест для проверки событий, которые заканчиваются 0-выми репликами собеседника  (для SK4509)
 */
class ZeroReplicasOfInterlocutor_SK4509_Test extends SeleniumTestHelper
{
    public function test_ZeroReplicas_SK4509 ()
    {
        $this->start_simulation();
        // E3.2 to E3.4 (visit-visit)
        $this->run_event('E3.2',"xpath=(//*[contains(text(),'Сделал')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это только на первый')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да я тоже об этом думал')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Чего тут считать!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо. Я просчитаю')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так! А мысли о том, как')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, наверное, так возможно')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Понимаешь, такой вариант был бы самым экономичным и быстрым')])");
        sleep(10);
        $this->waitForVisible("xpath=(//*[contains(text(),'Ладно, мне бежать пора. Я, кстати, рассчитываю данные')])");
        $this->optimal_click("xpath=(//*[contains(text(),' Иван, у меня только день до отпуска!')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Ну ты даешь!!! Мало того, что это я к тебе выбрался')])");
        sleep(10);

        $this->optimal_click('link=F14');
        $this->optimal_click('link=F36');

        // E12 to E12.1 (phone-phone)
        $this->run_event('E12',"xpath=(//*[contains(text(),'Я вас очень прошу, найдите сегодня любое время')])",'click');
        $this->waitForVisible("xpath=(//*[contains(text(),'Я вам перезвоню.')])");
        $this->transfer_time(4);
        $this->waitForVisible("xpath=(//*[contains(text(),'Нет, Босс завтра на выезде')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, буду в 18.00')])");

        // E12.4 to E12.7 (phone-visit)
        $this->run_event('E12.4', "xpath=(//*[contains(text(),'Давайте в 18.00, как договорились, я уже')])",'click');
        $this->waitForVisible("xpath=(//*[contains(text(),'Это не ко мне. Босс требует быть')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Добрый день, Валерий Семенович!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'В этот раз точно')])");
        $this->optimal_click("xpath=(//*[contains(text(),'мы все ваши замечания учли')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Цветовая гамма')])");
        $this->optimal_click("xpath=(//*[contains(text(),'работу делала Марина Крутько')])");
        $this->optimal_click("xpath=(//*[contains(text(),'а можно вопрос не по теме')])");

        $this->optimal_click('link=F41');

        // T2 to P6 (phone-plan)
        $this->run_event('T2',"xpath=(//*[contains(text(),'Иван, привет! Это Федоров')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Послушай, Иван, а мы можем встретиться, когда')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Ну прямо гора с плеч! Я сегодня в жутком цейтноте. ')])");
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons_active']['plan']);

        // T7.4 to M43 (phone-mail)
        $this->optimal_click("link=F38_1");
        $this->optimal_click("link=F38_2");
        $this->optimal_click("link=F38_3");
        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Ты его сделал?')])",'click');
        $this->waitForVisible("xpath=(//*[contains(text(),'Данные у вас в почте')])");
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons_active']['mail']);

        // RS2 to D2 (phone-document)
        $this->run_event('RS2',"xpath=(//*[contains(text(),'Доброе утро, Егор. Не совсем – я бюджетом занят.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я сегодня встречаюсь с первым')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, Егор! До отпуска времени')])");
        $this->optimal_click("xpath=(//*[contains(text(),'ты совершенно напрасно волнуешься')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Такое ощущение, что мы с тобой разными планами')])");
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons_active']['documents']);

        // E13 to P10 (visit-plan)
        $this->run_event('E13',"xpath=(//*[contains(text(),'я на совещание опаздываю')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Кхе….кхе…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты можешь рассказать подробнее, какие задачи')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, что мы об этом поговорили,')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай так договоримся. Ты дашь мне второй шанс.')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Давайте попробуем… И спасибо вам,')])");
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons_active']['plan']);

        $this->simulation_stop();
    }
}