<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Эффективная работа с почтой (Область обучения №5)
 */
class EmailEffectiveWork_SK2557_Test extends SeleniumTestHelper
{
    public function test_EmailEffectiveWork_SK2557()
    {
        $this->start_simulation("EmailEffectiveWork_SK2557_Test", 1);

        $this->clearEventQueueBeforeEleven('RST1');

        $this->run_event('T3.1',"xpath=(//*[contains(text(),'Егор, приветствую')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, но у тебя не больше пяти минут')])");
        sleep(3);

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом')])");
        $this->optimal_click("xpath=(//*[contains(text(),'сейчас поговорю с ним и уточню задание')])");
        sleep(3);

        $this->run_event('T7.2',"xpath=(//*[contains(text(),'Егор, ты хотел сегодня получить выгрузку данных! Она тебе все еще нужна?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Но что именно ты ждешь? Я об этом ничего не знаю, исполнитель – Трутнев – тоже. Он тебе два дня назад письмо отправил с уточнениями, ответа не получил!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Именно об этом я и говорю. Трутнев два дня назад написал тебе письмо с уточнениями, но ты не ответил. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Когда тебе нужны данные?')])");
        sleep(3);

        $this->run_event('T7.3',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным. Им нужно заполнить данными определенную форму за прошедшие девять месяцев. Она у тебя в почте.  Если будут вопросы – звони не откладывая.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, отложи все дела и сделай срочно. Думаю, двух часов тебе хватит. Перед отправкой перешли мне для проверки. ')])");
        sleep(3);

        $this->run_event('ET1.1', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет. Сейчас же приступаю к доработке')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь')])");
        sleep(3);

        $this->clearEventQueueBeforeEleven('RST2');
        $this->clearEventQueueBeforeEleven('RST3');

        $this->run_event('M8');
        sleep(2);
        $this->run_event('M6');
        sleep(2);
        $this->run_event('M47');
        sleep(2);
        $this->run_event('M73');
        sleep(2);
        $this->run_event('M7');
        sleep(2);

        $this->run_event('ET10', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас найду и перешлю')])");
        sleep(3);

        $event="RVT1";
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['door'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['deny']);
        $event .= '.1';
        $this->run_event($event, Yii::app()->params['test_mappings']['icons_active']['door'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['deny']);

        $this->run_event('RS2',"xpath=(//*[contains(text(),'Доброе утро, Егор. Не совсем – я бюджетом занят.')])", 'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я начну работу по проекту только после отпуска')])");
        sleep(3);

        $this->run_event("MS45");
        sleep(3);

        // MS69
        $this->write_mail_unidentified();
        //$this->optimal_click(Yii::app()->params['test_mappings']['icons']['mail']); // extra
        if ($this->isElementPresent("css=.NEW_EMAIL")) {
            $this->optimal_click("css=.NEW_EMAIL");
        }
        $this->addRecipient("//ul[contains(@class,'ui-autocomplete')]/li[21]/a");
        $this->addTheme("xpath=(//*[contains(text(), 'Квартальный план')])");
        $this->addAttach("Квартальный план_4 кв_2013");
        $this->optimal_click("css=.SEND_EMAIL");

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['settings']);
        $this->optimal_click("css=.volume-control.control-mail.volume-on");
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['settings']);

        sleep(3);
        $this->optimal_click("xpath=(//*[contains(text(),'бюджет логистики')])");

        // MS28
        $this->write_new_email(Yii::app()->params['test_mappings']['mail_contacts']['bobr'],"Бюджет производства прошлого года","Бюджет производства_2013_утв");

        // MS42
        $this->write_forward_email("форма по задаче от логистики, срочно!", "//ul[contains(@class,'ui-autocomplete')]/li[35]/a" );

        // MS35
        $this->write_new_email(Yii::app()->params['test_mappings']['mail_contacts']['denejnaya'],"Сводный бюджет","Сводный бюджет_2014_план");

        // читаем M8
        $this->optimal_click("xpath=(//*[contains(text(),'!проблема с сервером!')])");

        // отправляем MS20
        $this->write_new_email(Yii::app()->params['test_mappings']['mail_contacts']['denejnaya'],"Служебная записка","");

        // читаем письмо M6
        $this->optimal_click("xpath=(//*[contains(text(),'консультанты и новый проект')])");

        // читаем и пересылаем M47
        $this->write_forward_email("данные по рынку, срочно", "//ul[contains(@class,'ui-autocomplete')]/li[35]/a");

        // читаем письмо M7
        $this->write_reply_email("пришлите срочно пожелания!");

        $this->addTaskToPlan("обучение регионального аналитика","102");

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);

        $this->clearEventQueueBeforeEleven('RST4');
        $this->clearEventQueueBeforeEleven('RST5');

        $this->optimal_click("link=F38_3");
        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Ты его сделал?')])",'click');
        sleep(3);

        $this->run_event('ET2.1', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'в прошлый раз нам пришлось презентацию за день делать!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, прямо сейчас проконтролирую, как идет подготовка.')])");
        sleep(3);

        $this->optimal_click("xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию')])");
        $this->waitForVisible("xpath=(//*[contains(text(),'Я над ней работаю.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это хорошо, что ')])");
        sleep(3);

        $this->run_event('ET3.1', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, Иван')])");
        sleep(3);

        $this->run_event('ET3.3', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Здравствуйте. Любопытно')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Извините, давайте созвонимся после отпуска, я сейчас очень занят')])");
        sleep(3);

        $this->run_event('ET8',Yii::app()->params['test_mappings']['icons_active']['door'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['deny']);
        sleep(3);

        $this->run_event('M4');
        sleep(3);
        $this->run_event('M76');
        sleep(3);
        $this->run_event('M5');
        sleep(3);
        $this->run_event('M71');
        sleep(3);
        $this->run_event('M9');
        sleep(3);
        $this->run_event('M3');
        sleep(3);
        $this->run_event('M77');
        sleep(3);
        $this->run_event('M72');
        sleep(3);
        $this->run_event('M2');
        sleep(3);
        $this->run_event('M74');
        sleep(3);
        $this->run_event('M66');
        sleep(3);

        $this->optimal_click(Yii::app()->params['test_mappings']['icons_active']['mail']);
        sleep(14);

//        $this->write_mail_unidentified();
//        sleep(3);
//        $this->addTaskToPlan("предложения","23");
//        $this->addTaskToPlan("график отпусков срочно!","26");
//        $this->addTaskToPlan("твои аналитики","19");
//        $this->addTaskToPlan("по вашей заявке","113");
//        $this->optimal_click("xpath=(//*[contains(text(),'выгрузка для логистов')])");

        $this->write_reply_email("привет");
        $this->write_reply_email("короткая просьба");
        $this->write_reply_email("Презентация для ГД_рабочая версия");
        $this->write_reply_email("отчет срочно!");
        $this->write_forward_email("отчет срочно!","//ul[contains(@class,'ui-autocomplete')]/li[35]/a");
        $this->write_replyAll_email("срочно! Требования клиентов","xpath=(//*[contains(text(), 'Железный')])");
        $this->write_reply_email("запрос Крутько");

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);

        $this->clearEventQueueBeforeEleven('RST7');
        $this->clearEventQueueBeforeEleven('RST8');

        $this->run_event('ET9', Yii::app()->params['test_mappings']['icons_active']['phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, вопрос в чем?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");
        sleep(5);

        $this->run_event('ET13',Yii::app()->params['test_mappings']['icons_active']['door'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, могу я чем-то помочь')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А что именно должно поменяться и почему')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты можешь рассказать подробнее, какие задачи для тебя интересны')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же ты раньше об этом не говорила')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай так договоримся')])");
        sleep(1);

        $this->clearEventQueueBeforeEleven('RST9');

        $this->optimal_click("xpath=(//*[contains(text(),'Ясно, Раиса Романовна')])");

        $this->optimal_click("xpath=(//*[contains(text(),'Сделал')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это только на первый взгляд')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да я тоже об этом думал')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Чего тут считать')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Наверное')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мысли о том')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Иван, сейчас я не могу тебе ответить')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо')])");
        sleep(1);

        $this->clearEventQueueBeforeEleven('RST10');
        sleep(1);

        $this->run_event('ET15',Yii::app()->params['test_mappings']['icons_active']['door'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['visit']['allow']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, прошу')])");
        sleep(10);

        $this->run_event('M1');
        sleep(3);
        $this->run_event('M10');
        sleep(3);
        $this->run_event('M70');
        sleep(3);
        $this->run_event('M60');
        sleep(3);
        $this->run_event('M64');
        sleep(3);
        $this->run_event('M67');
        sleep(3);
        $this->run_event('M61');
        sleep(3);
        $this->run_event('M68');
        sleep(3);
        $this->run_event("MS53");
        sleep(3);
        $this->run_event('M62');
        sleep(3);
        $this->run_event('M63');
        sleep(3);
        $this->run_event('M75');
        sleep(3);
        $this->run_event('M65');
        sleep(3);
        $this->run_event('M69');
        sleep(3);
        $this->run_event("MS25");
        sleep(3);
        $this->run_event("MS51");
        sleep(3);
        $this->run_event("MS39");
        sleep(5);

        $this->write_mail_unidentified();
        if ($this->isElementPresent("css=.NEW_EMAIL")) {
            $this->optimal_click("css=.NEW_EMAIL");
        }
        $this->addRecipient(Yii::app()->params['test_mappings']['mail_contacts']['denejnaya']);
        $this->addTheme("xpath=(//*[contains(text(), 'Сводный бюджет: итоговые корректировки')])");
        $this->addAttach("Сводный бюджет_2014_план");
        $this->optimal_click("css=.SEND_EMAIL");

        $this->write_new_email(Yii::app()->params['test_mappings']['mail_contacts']['analitics'],"Приглашение: новая система премирования","");

        $this->optimal_click("xpath=(//*[contains(text(),'новый бюджет по производству')])");
        $this->optimal_click("xpath=(//*[contains(text(),'ДР отца')])");

        $this->write_replyAll_email("срочно! Отчетность", "");
        $this->write_reply_email("Презентация для ГД_итог");
        $this->write_reply_email("адрес клиента");
        $this->write_forward_email("вакцинация!", "//ul[contains(@class,'ui-autocomplete')]/li[14]/a");
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);
        sleep(5);

        $this->simulation_showLogs();
        //assertions

    }

    private function write_new_email($recipient, $theme, $attachment)
    {
        $this->optimal_click("css=.NEW_EMAIL");
        $this->addRecipient($recipient);
        sleep(2);
        $this->addTheme("xpath=(//*[contains(text(), '$theme')])");
        if ($attachment!="")
        {
            $this->addAttach($attachment);
        }
        $this->optimal_click("css=.SEND_EMAIL");
    }

    private function write_reply_email($theme)
    {
        $this->optimal_click("xpath=(//*[contains(text(), '$theme')])");
        $this->optimal_click("css=.REPLY_EMAIL");
        $this->optimal_click("css=.SEND_EMAIL");
    }

    private function write_replyAll_email($theme, $copy_rec)
    {
        $this->optimal_click("xpath=(//*[contains(text(), '$theme')])");
        $this->optimal_click("css=.REPLY_ALL_EMAIL");
        if ($copy_rec!="")
        {
            $this->optimal_click(Yii::app()->params['test_mappings']['mail']['add_copy_rec']);
            $this->waitForVisible($copy_rec);
            $this->mouseOver($copy_rec);
            $this->optimal_click($copy_rec);
        }
        $this->optimal_click("css=.SEND_EMAIL");
    }

    private function addTaskToPlan($theme,$task_id)
    {
        $this->optimal_click("xpath=(//*[contains(text(), '$theme')])");
        $this->optimal_click("css=.ADD_TO_PLAN");
        $this->optimal_click("css=.mail-plan-item.mail-task-$task_id");
        $this->optimal_click("css=.mail-plan-btn>span");
        //$this->optimal_click(Yii::app()->params['test_mappings']['icons']['close1']);
    }

    private function write_forward_email($theme, $recipient)
    {
        $this->optimal_click("xpath=(//*[contains(text(),'$theme')])");
        $this->optimal_click("css=.FORWARD_EMAIL");
        $this->addRecipient($recipient);
        $this->optimal_click("css=.SEND_EMAIL");
    }

}


