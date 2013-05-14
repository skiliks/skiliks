<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Управление людьми (Область обучения №2)
 */
class ManagementPeople_Test extends SeleniumTestHelper {
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testManagementPeople_Positive()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        // Delegation

        $this->run_event('MS28');
        sleep(2);

        $this->run_event('T6.2',"xpath=(//*[contains(text(),'Марина, запиши, пожалуйста, важную информацию по презентации.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет-нет. Ее надо распечатать. Запиши, пожалуйста.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Главное, чтобы хватило. Сорок копий.')])");

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


        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->optimal_click("xpath=//div/div[4]/form[1]/fieldset/table[1]/thead/tr/th[6]/a");
        $this->assertTrue($this->verify_flag('F14','1'));
        sleep(5);

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

        $this->run_event('T5.2',"xpath=(//*[contains(text(),'Марина, я по поводу презентации. Спасибо, что прислала вовремя, как договаривались. Работа полностью соответствует требованиям. Мне и корректировать нечего.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Я дам тебе знать, что думает Босс, после встречи с ним. ')])");

        $this->run_event('M10');
        sleep(2);
        $this->run_event('MS37');
        sleep(2);

        // Resources

        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, привет! Что там с презентацией для Генерального? ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Отлично, одной проблемой меньше. Жду в 15.30')])");

        $this->run_event('T7.4',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Ты его сделал?')])",'click');

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(60);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['management3'],"100");
    }


    public function testManagementPeople_Negative()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();

        //Delegation

        $this->run_event('E10',"xpath=(//*[contains(text(),'Хорошо, сейчас найду и перешлю.')])",'click');
        sleep(2);
        $this->run_event('E8.5',"xpath=(//*[contains(text(),'Сергей, нужна помощь! Возьми ручку и записывай. Готов?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Так, отложи в сторону своих логистов, мне всего пять твоих минут нужно.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я тебя не задержу. Надо написать служебку на имя Денежной на выделение средств за рамками бюджета на покупку нового сервера для отдела. Я тебе письмо от АйТишников перешлю – данные возьми оттуда.')])");
        sleep(20);

        $this->run_event('ET12.2',"xpath=(//*[contains(text(),'Нет у меня никакой презентации. У Крутько спрашивайте. Она обещала, а не сделала.')])",'click');
        sleep(2);

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов по выгрузке данных. Трудякин просил сегодня! Ты его сделал?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну кто же так делает? Что же ты молчишь? У тебя задание уже два дня висит! ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Не ответил? А ты ему звонил?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Сергей, это твое задание! Ты должен выяснить детали у заказчика! Прямо сейчас и звони. Результат доложишь!')])");
        sleep(2);

        $this->run_event('T7.1.1',"xpath=(//*[contains(text(),'Я сказал доложить о результате! А пока результата нет. Сообщишь мне задание, когда уточнишь его у Трудякина.')])",'click');
        sleep(2);
        $this->run_event('M8');
        sleep(2);
        $this->run_event('MS21');
        sleep(2);
        $this->run_event('MS22');
        sleep(2);
        $this->run_event('MS23');
        sleep(2);
        $this->run_event('MS27');
        sleep(2);
        $this->run_event('MS29');
        sleep(2);

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('E8.3');
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Конечно читал. Хорошее письмо, обстоятельное!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Семен, а у тебя наверняка в бюджете статейка есть на непредвиденные расходы. Это ведь форс-мажор?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понял тебя, скажу своему аналитику, чтобы срочно писал служебку.')])");
        sleep(2);

        $this->run_event('E2',"xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать! А аналитика, который тогда напортачил, я уже уволил.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, прямо сейчас проконтролирую, как идет подготовка.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, привет! Что там с презентацией для Генерального? ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это хорошо, что задача ясна. Я бы хотел через час просмотреть промежуточный вариант. Пришли, пожалуйста.')])");
        sleep(10);

        $this->run_event('T6.2',"xpath=(//*[contains(text(),'Марина, запиши, пожалуйста, важную информацию по презентации.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну что ты! Конечно нет. Он просит распечатать презентации. Передай, пожалуйста, его секретарю сорок экземпляров к пяти вечера в среду')])");
        $this->optimal_click("xpath=(//*[contains(text(),'По-моему, в семнадцать.')])");
        sleep(2);

        // Feedback
        $this->run_event('T5.2',"xpath=(//*[contains(text(),'Марина, я посмотрел презентацию, все по делу.  Замечаний у меня нет. Продолжай.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Я дам тебе знать, что думает Босс, после встречи с ним. ')])");
        sleep(2);

        $this->run_event('E13',"xpath=(//*[contains(text(),'Марина, могу я чем-то помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Кхе….кхе…')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Вот это новость! Ты, наверное, устала.')])");
        sleep(2);

        // Optimal_person
        $this->run_event('RS1.1',"xpath=(//*[contains(text(),'Привет, Сергей! Ты очень занят?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Понимаю. Много времени моя просьба не займет. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это так. Но речь идет всего о пятнадцати минутах. Сбегай в шиномонтаж на нашей улице – забери мою машину, пожалуйста. Я совсем никак не могу вырваться.  ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, заскочи, только быстро! Работа не должна страдать. ')])");
        sleep(2);

        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, файл готовил не я, а Трутнев. Я непременно проверю в следующий раз.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, я готовлю презентацию для Босса, попрошу Трутнева поправить ошибку  в ближайшее время и переслать вам файл.')])");
        sleep(2);

        $this->run_event('MS54');
        sleep(2);
        $this->run_event('MS21');
        sleep(2);
        $this->run_event('MS22');
        sleep(2);
        $this->run_event('M8');
        sleep(2);
        $this->run_event('MS27');


        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(180);
        //$this->waitForVisible(Yii::app()->params['test_mappings']['log']['goals']);
        //$this->assertText("xpath=//div[1]/div/div[2]/table[17]/tbody/tr[1]/td[4]","0");
        //$this->assertText("xpath=//div[1]/div/div[2]/table[17]/tbody/tr[2]/td[4]","0");
    }


    public function testManagementPeople_Problems()
    {
        $this->markTestIncomplete();
        $this->start_simulation();

        //Delegation
        $this->run_event('E2.4',"xpath=(//*[contains(text(),'Марина, срочно пересылай мне презентацию для Генерального! Босс сам звонил и интересовался!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай мы все-таки посмотрим, что у тебя там получается с учетом требований Босса. Шли мне презентацию прямо сейчас.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Послушай, так здесь и смотреть нечего!  И как ты собираешься уложиться в срок?')])");

        $this->run_event('E2.10',"xpath=(//*[contains(text(),'Не ожидал от тебя такого легкомыслия. Ты хоть понимаешь, для кого ты это делаешь? Да ведь это просто саботаж какой-то!  ')])",'click');

        $this->run_event('E2.8',"xpath=(//*[contains(text(),'Марина, что с тобой? Возьми себя в руки!  Мне тоже жаль, что все сегодня так складывается, но во многом это не моя вина . Возвращайся и отправь мне все, что есть по презентации. ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'ты, кем, ангелом считаешь? Тогда уж и я скажу все, что думаю! Не надо ставить себя выше других! Ты что, считаешь себя самой умной?! Так ты нигде не уживешься! Успокойся и пришли документы.')])");

        $this->run_event('E1.3',"xpath=(//*[contains(text(),'Сергей, привет! Ты не мог бы мне помочь? У меня тут полный аврал.  Крутько занята, только на тебя надежда. Будем делать бюджет, сводный.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или поздно придется этим заниматься. Если ты не подключишься, мне придется делать самому, а у меня столько дел перед отпуском!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уже сказал, что дело срочное и что ты мне нужен. Иди и начинай работать.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Однако тебе все-таки придется выполнить это задание. Пересылаю тебе файл. Смотри внимательно и не тяни с вопросами. На все про все у тебя два часа.')])");

        $this->run_event('E8.3',"xpath=(//*[contains(text(),'Конечно читал. Хорошее письмо, обстоятельное!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Семен, а у тебя наверняка в бюджете статейка есть на непредвиденные расходы. Это ведь форс-мажор?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понял тебя,  скажу своему аналитику, чтобы срочно служебку писал.')])");

        $this->run_event('E11',"xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения. Впредь такого не будет.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, я готовлю презентацию для Босса, попрошу Трутнева поправить ошибку  в ближайшее время и переслать вам файл.')])");

        $this->run_event('E12.1',"xpath=(//*[contains(text(),'Может мой аналитик подойти вместо меня?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В понедельник, скажем в 10.00, будет моя сотрудница Марина Крутько.')])");

        $this->run_event('E12.5',"xpath=(//*[contains(text(),'Но мы ведь уже договорились, и я успел поменять мой график. Крутько придет в понедельник!')])",'click');

        $this->run_event('RS1',"xpath=(//*[contains(text(),'Кто вам нужен?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, чем могу помочь?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я не могу уйти, мне надо отпрашиваться у руководителя!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас от меня придет человек.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Доброе утро, Сергей! Нужна твоя помощь!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понимаю. Много времени моя просьба не займет. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это так. Но речь идет всего о пятнадцати минутах. Сбегай в шиномонтаж на нашей улице – забери мою машину, пожалуйста. Я совсем никак не могу вырваться.  ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, заскочи, только быстро! Работа не должна страдать. ')])");

        $this->run_event('RS2',"xpath=(//*[contains(text(),'Доброе утро, Егор. Не совсем – я бюджетом занят.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Какой план? Я бюджетом занят!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я могу тебе предложить достойную альтернативу – повидайся с моим лучшим аналитиком Мариной Крутько! Я планирую ее в проект вовлекать.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сегодня вечером, после шести! ')])");

        $this->run_event('MS68');
        sleep(2);
        $this->run_event('MS70');
        sleep(2);

        // Feedback
        $this->run_event('E13',"xpath=(//*[contains(text(),'Марина, могу я чем-то помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, а мы можем о твоих размышлениях поговорить в другой раз?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да брось, Марина, мы ведь работаем бок о бок целый год. Все, конечно, бывает, но мы двигаемся, работа интересная, ты на хорошем счету, чего еще не хватает?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне, в компании или типе задач?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну вот видишь… Ты и сама не знаешь…')])");

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов по выгрузке данных. Трудякин просил сегодня! Ты его сделал?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Так, прекрасно! Сроки опять летят!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы же говорили, что в письмах людям выше тебя статусом я должен стоять в копии! ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо,  сейчас поговорю с ним и уточню задание! Но впредь учти – детализация задачи – часть твоей работы! ')])");

        $this->run_event('E1.3.2',"xpath=(//*[contains(text(),'Ты что, не знаешь, как прибыль считается? Я тебя для чего тут держу?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вот тебе и вся экономика, плюс да минус. Давай, работай. А если чего не ясно – в методику смотри, сейчас перешлю. ')])");

        $this->run_event('E2.7',"xpath=(//*[contains(text(),'Вот уж не ждал от тебя такого легкомыслия! Ведь это не обычный отчет! Это заказ Босса!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Потрясающая безответственность! Вот уж от тебя никак не ожидал!')])");

        // Resources
        $this->run_event('E2',"xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать! А аналитика, который тогда напортачил, я уже уволил.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Да, прямо сейчас проконтролирую, как идет подготовка.')])");

        $this->run_event('T7.3',"xpath=(//*[contains(text(),'Я по поводу задания от логистов. Поговорил с Трудякиным. Им нужно заполнить данными определенную форму за прошедшие девять месяцев. Она у тебя в почте.  Если будут вопросы – звони не откладывая.')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, нужно сегодня и срочно. У тебя есть два часа. Поставь меня в копию при отправке Трудякину.')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        sleep(30);
        //$this->waitForVisible(Yii::app()->params['test_mappings']['log']['goals']);
        //$this->assertText("xpath=//div[1]/div/div[2]/table[17]/tbody/tr[1]/td[4]","0");
        //$this->assertText("xpath=//div[1]/div/div[2]/table[17]/tbody/tr[2]/td[4]","0");
    }
}