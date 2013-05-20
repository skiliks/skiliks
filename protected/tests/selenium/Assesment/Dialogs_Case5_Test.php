<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тест на диалоги, Case 5.
 * Пошагово запускаем диалоги из /Users/Tony/Dropbox/projectx - development/1. Documentation/1.1 Scenario/1.1.1 Оценка/Тесты/Расчет оценки_тест3_final.xls
 * После чего в Simulation points сверяем суммы оценок поведений positive, negative & personal по mail matrix и all dialogs
 */
class Dialogs_Case5_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function test_Dialogs_for_SK1791()
    {
        //$this->markTestIncomplete();
       /* $mail_code = array ('MY2','MS22','MY2','MY2','MS23','MY2','M11','MS27','M11','MS70','MS23','MS54');
        $window = array ('mail main','mail new','mail main','mail main','mail new','mail main','mail main',
            'mail new','mail main','mail new','mail new','mail new');
        $Mail_log = array ( $window, $mail_code);*/

        $this->start_simulation();
        $this->optimal_click('link=F32');
        sleep(5);
        $this->run_event('E1.2',"xpath=(//*[contains(text(),'Марина, есть срочная работа')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать? ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да уж, ситуация')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Сергей, привет! Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или поздно придется этим заниматься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки придется выполнить это задание')])");
        //тут идет отправка письма MS22 фант. образом
        sleep(10);

        $this->run_event('E1.3.2',"xpath=(//*[contains(text(),'У меня тут методика где-то была по сводному бюджету')])", 'click');
        //а тут идет отправка MS23
        sleep(10);

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('E1.3.3',"xpath=(//*[contains(text(),'Как твои дела?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вообще-то я про сводный бюджет')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, отличная методика, я сам ее и составлял')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ясно, сроки мы с тобой провалили')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, еще раз здравствуйте')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Вы только не волнуйтесь,  бюджет немного задерживается')])");

        //для запуска E2.2 нужен флаг F3
        $this->optimal_click('link=F3');
        $this->run_event('E2.2',"xpath=(//*[contains(text(),'Марина, пожалуйста, вышли прямо сейчас все')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вот это да! Ладно, отложи пока сводный бюджет и займись презентаций')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, что с тобой? Возьми себя в руки!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Прекрасно, сообщи в отдел персонала о своем решении')])");

        $this->run_event('E8.5',"xpath=(//*[contains(text(),'Сергей, нужна помощь! Возьми ручку и записывай')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Так, отложи в сторону своих логистов')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Уйдешь, когда работу закончишь')])");
        //отправка MS27
        sleep(10);

        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, файл готовил не я, а Трутнев')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'попрошу Трутнева поправить ошибку  в ближайшее время и переслать вам файл')])");

        $this->run_event('MS70');
        sleep(2);
        $this->run_event('RS1.1',"xpath=(//*[contains(text(),'Привет, Сергей! Ты очень занят?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, мое задание')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это так. Но речь идет всего о пятнадцати минутах')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну что ты, Сергей! За кофе по дороге на работу зайдешь')])");

        $this->run_event('MS21');
        sleep(2);
        $this->run_event('MS23');
        sleep(2);
        $this->run_event('MS54');
        sleep(5);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        //sleep(20);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        sleep(1);
        $this->checkSimPoints('2','-31');
        $this->checkLearningArea('0.00','0.00','0.00','4.55','11.23','5','12.5','0.00');

    }
}
