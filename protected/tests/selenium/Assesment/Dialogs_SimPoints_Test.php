<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты на диалоги, Case 1-6.
 * Пошагово запускаем диалоги из /Users/Tony/Dropbox/projectx - development/1. Documentation/1.1 Scenario/1.1.1 Оценка/Тесты/Расчет оценки_тест3_final.xls
 * После чего в Simulation points сверяем суммы оценок поведений positive, negative & personal по mail matrix и all dialogs
 */
class Dialogs_SimPoints_Test extends SeleniumTestHelper
{
    //тест1
    public function test_Dialogs_for_SK1390()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("test_Dialogs_for_SK1390", 1);
        $this->run_event('ET1.1', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна,  я как раз собираюсь')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я пока не знаю, сколько времени мне потребуется')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понял, открываю файл')])");
        sleep(2);
        $this->run_event('ET2.1', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
        sleep(2);
        $this->run_event('ET2.3', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Непременно, сейчас запланирую время на проверку')])");
        sleep(2);
        $this->simulation_showLogs();
        $this->checkSimPoints('7','0');
        sleep(2);
    }

    //тест2
    public function test_Dialogs_for_SK1395()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("test_Dialogs_for_SK1395", 1);
        sleep(2);
        $this->run_event('E1',"xpath=(//*[contains(text(),' я как раз собираюсь подключить нашего лучшего аналитика')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь')])");
        sleep(2);
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "E8.3");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, прости, Семен. Сегодня просто сумасшедший день')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Семен, а у тебя наверняка в бюджете статейка есть на непредвиденные расходы')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас вернусь и напишу служебку. Спасибо за информацию! ')])");
        sleep(2);
        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, буду в 18.00')])");
        sleep(3);
        $this->optimal_click('link=F14');
        $this->optimal_click('link=F36');
        sleep(1);
        $this->run_event('E12.4',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])",'click');
        sleep(2);
        $this->simulation_showLogs();
        $this->checkSimPoints('3','0');
        sleep(2);
    }

    //тест3
    public function test_Dialogs_for_SK910()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("test_Dialogs_for_SK910", 1);
        $this->optimal_click('link=F14');
        $this->optimal_click('link=F36');
        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию для Генерального')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше. Жду в 15.30')])");
        sleep(2);
        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько')])");
        sleep(2);
        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Действительно, повезло! Уже бегу!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Кхе-кхе…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, доволен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ваша презентация была не единственным его промахом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это наши корпоративные цвета')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы вместе с сотрудниками. Они готовили – я проверял.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошего вам выступления, Валерий Семенович!')])");
        sleep(10);
        $this->run_event('MS27');
        sleep(2);
        $this->run_event('MS68');
        sleep(2);
        $this->run_event('MS70');
        sleep(2);

        $this->simulation_showLogs();
        $this->checkSimPoints('8','-7');
        sleep(2);
    }

    //тест4
    public function test_Dialogs_for_SK1790()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("test_Dialogs_for_SK1790", 1);
        $this->optimal_click('link=F39');

        $this->run_event('RST1',Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Кто вам нужен?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, а вы кто?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Слушайте, у меня времени нет, я смогу только в обед!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас от меня придет человек.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Привет, Сергей! Ты очень занят?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, мое задание')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это так. Но речь идет всего о пятнадцати минутах')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну что ты, Сергей! За кофе по дороге на работу зайдешь')])");

        $this->run_event('RS1.2',"xpath=(//*[contains(text(),'тебе огромное за помощь!')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Кто вам нужен?')])");

        $this->run_event('E1',"xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну, с помощью Крутько я должен управиться в эти сроки')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, бросай все свои дела')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Закончила? Теперь слушай сюда')])");
        //отправка MS21
        sleep(10);
        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Сергей, нужно сделать бюджет. Срочно.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В чем  именно ты не уверен?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уже сказал, что дело срочное и что ты мне нужен')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки придется выполнить это задание')])");
        //отправка MS22

        $this->assertTrue($this->verify_flag('F3','1'));
        $this->run_event('E2.2',"xpath=(//*[contains(text(),'Босс звонил. Требует эту презентацию.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),' Ах да. Помню. Шли, что есть вместе с твоими мыслями и прошлогодней презентацией')])");
        sleep(2);
        $this->run_event('E8.3',"xpath=(//*[contains(text(),'Конечно читал. Хорошее письмо, обстоятельное')])", 'click');
        sleep(2);
        $this->optimal_click("xpath=(//*[contains(text(),'Понял тебя,  скажу своему аналитику, чтобы срочно служебку писал')])");
        $this->run_event('E8.5',"xpath=(//*[contains(text(),'Сергей, удобно тебе говорить?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну ладно, набери меня, когда освободишься')])");
        sleep(2);
        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения. Впредь такого не будет')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'попрошу Трутнева поправить ошибку')])");
        sleep(10);
        $this->run_event('MS68');
        sleep(2);
        $this->run_event('MS70');
        sleep(2);
        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Но я с понедельника в отпуске')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько')])");
        sleep(2);
        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Но мы ведь уже договорились, и я успел поменять мой график')])", 'click');
        sleep(2);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "40");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        $this->run_event('RS2',"xpath=(//*[contains(text(),'Доброе утро, Егор. Не совсем – я бюджетом занят.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Какой план? Я бюджетом занят!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я могу тебе предложить достойную альтернативу – повидайся с моим лучшим аналитиком Мариной Крутько')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сегодня вечером, после шести! ')])");
        sleep(10);
        $this->simulation_showLogs();
        $this->checkSimPoints('1','-36.5');
        sleep(2);
    }

    //тест5
    public function test_Dialogs_for_SK1791()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("test_Dialogs_for_SK1791", 1);
        $this->optimal_click('link=F32');
        $this->optimal_click('link=F39');
        sleep(5);
        $this->run_event('RST1',Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Кто вам нужен?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, а вы кто?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Слушайте, у меня времени нет, я смогу только в обед!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас от меня придет человек.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Привет, Сергей! Ты очень занят?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно, мое задание')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это так. Но речь идет всего о пятнадцати минутах')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну что ты, Сергей! За кофе по дороге на работу зайдешь')])");
        sleep(2);
        $this->run_event('E1.2',"xpath=(//*[contains(text(),'Марина, есть срочная работа')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать? ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да уж, ситуация')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Сергей, привет! Ты не мог бы мне помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или поздно придется этим заниматься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки придется выполнить это задание')])");
        //тут идет отправка письма MS22 фант. образом
        sleep(10);
        if (true===$this->isElementPresent("xpath=//*[@id='messageSystemMessageDiv']/div"))
        {
            $this->optimal_click("xpath=(//*[contains(text(),'Не сохранять')])");
            $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        }
        $this->run_event('E1.3.2',"xpath=(//*[contains(text(),'У меня тут методика где-то была по сводному бюджету')])", 'click');
        //а тут идет отправка MS23
        sleep(10);
        if (true===$this->isElementPresent("xpath=//*[@id='messageSystemMessageDiv']/div"))
        {
            $this->optimal_click("xpath=(//*[contains(text(),'Не сохранять')])");
            $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        }
        sleep(10);
        $this->run_event('E1.3.3',"xpath=(//*[contains(text(),'Как твои дела?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вообще-то я про сводный бюджет')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, отличная методика, я сам ее и составлял')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ясно, сроки мы с тобой провалили')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, еще раз здравствуйте')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Вы только не волнуйтесь,  бюджет немного задерживается')])");
        //для запуска E2.2 нужен флаг F3
        $this->optimal_click('link=F3');
        sleep(2);
        $this->run_event('E2.2',"xpath=(//*[contains(text(),'Марина, пожалуйста, вышли прямо сейчас все')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вот это да! Ладно, отложи пока сводный бюджет и займись презентаций')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что это с тобой случилось?! Столько агрессии')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Прекрасно, сообщи в отдел персонала о своем решении')])");
        sleep(2);
        $this->run_event('E8.5',"xpath=(//*[contains(text(),'Сергей, нужна помощь! Возьми ручку и записывай')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Так, отложи в сторону своих логистов')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Уйдешь, когда работу закончишь')])");
        //отправка MS27
        sleep(10);
        if (true===$this->isElementPresent("xpath=//*[@id='messageSystemMessageDiv']/div"))
        {
            $this->optimal_click("xpath=(//*[contains(text(),'Не сохранять')])");
            $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        }
        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, файл готовил не я, а Трутнев')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'попрошу Трутнева поправить ошибку  в ближайшее время и переслать вам файл')])");
        sleep(2);
        $this->run_event('MS70');
        sleep(2);
        $this->run_event('MS21');
        sleep(2);
        $this->run_event('MS23');
        sleep(2);
        $this->run_event('MS54');
        sleep(5);
        $this->simulation_showLogs();
        $this->checkSimPoints('0','-32');
        sleep(2);
    }

    //тест6
    public function test_Dialogs_for_SK1792()
    {
        //$this->markTestIncomplete();
        $this->start_simulation("test_Dialogs_for_SK1792", 1);
        sleep(2);
        $this->clearEventQueueBeforeEleven('RST1');
        sleep(2);
        $this->run_event('E1.3.2',"xpath=(//*[contains(text(),'Я тебя для чего тут держу?')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, я сам все сделаю, письмо от логистов у меня тоже есть')])");
        sleep(2);
        $this->run_event('E2.7',"xpath=(//*[contains(text(),'Вот уж не ждал от тебя такого легкомыслия!')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Потрясающая безответственность!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Столько агрессии…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тогда уж и я скажу все, что думаю')])");
        sleep(2);
        $this->run_event('E13',"xpath=(//*[contains(text(),'я на совещание опаздываю')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Кхе….кхе…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну вот видишь…')])");
        sleep(2);
        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");
        sleep(2);
        $this->simulation_showLogs();
        $this->checkSimPoints('13.167','-9');
        sleep(2);
    }
}
