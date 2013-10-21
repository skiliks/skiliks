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

        //TODO: дописать кейсы разговор по телефону-разговор по телефону, разговор по телефону-визит, развор по телефону-план, разговор по телефону-документ

        // E3.2 to E3.4 (visit-visit)
        $this->run_event('E3.2',"xpath=(//*[contains(text(),'Сделал')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что просходит')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да я тоже об этом думал')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Чего тут считать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так! А мысли')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, наверное, так возможно')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Понимаешь, такой вариант был бы самым экономичным и быстрым')])");
        sleep(10);
        $this->waitForVisible("xpath=(//*[contains(text(),'Ладно, мне бежать пора. Я, кстати, рассчитываю данные')])");
        $this->optimal_click("xpath=(//*[contains(text(),' Иван, у меня только день до отпуска!')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Ну ты даешь!!! Мало того, что это я к тебе выбрался')])");
        sleep(10);


        $this->simulation_stop();
    }
}