<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Устойчивость к манипуляциям и давлению (Область обучения №10)
 */
class Result_Orientation_Test extends SeleniumTestHelper
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function test_result_orientation()
    {
        //$this->markTestIncomplete();
        $this->start_simulation();
        $this->optimal_click('link=F32');
        sleep(5);

        $this->run_event('E1',"xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет. Сейчас же приступаю к доработке. ')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Ну, с помощью Крутько я должен управиться в эти сроки. ')])");
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Марина, мне срочно необходима твоя помощь.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А мне что делать? А ты не можешь пока')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да уж, ситуация... Значит так, быстренько зови сюда Трутнева, попробую его подключить. ')])");
        sleep(5);
        $this->optimal_click("xpath=(//*[contains(text(),'Сергей, нужно сделать бюджет. Срочно. Тебе придется этим заняться.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Тебе же все равно рано или поздно придется этим заниматься. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я знаю, что ты справишься')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно. Я понял. Сделаю сам. Спасибо. Можешь идти. ')])");

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "02");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->optimal_click('link=F20');
        $this->run_event('E3.2',"xpath=(//*[contains(text(),'Сделал, прямо перед тобой отправил')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Давай по порядку. Мы получаем от вас данные о продажах в виде простого файла формата word, которые потом мой аналитик вбивает в информационную систему вручную.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Ну, знаешь, мы ведь в одной упряжке. Сегодня мне что-то нужно, завтра – тебе. Вот, например, ты просил у меня данные по рынку, которые мы собираем по своим клиентам. Я же готов тебе помочь!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да я тоже об этом думал, но, увы, эта идея противоречит современной политике. Я имею в виду сокращение затрат. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Чего тут считать! Понятно же, что проблема есть и ее надо решать.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Наверное, ты прав. Может, пока на этом и остановимся? Мне надо подумать-посчитать.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так! А мысли о том, как можно реализовать эту автоматическую пересылку и перекачку, у тебя есть?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Иван, сейчас я не могу тебе ответить, нам потребуется руководитель нашего подразделения информационных технологий. А я его, каюсь, не пригласил!')])");
        sleep(10);
        $this->run_event('E8',"xpath=(//*[contains(text(),'Привет, Семен! С бюджетом покончено, можешь меня поздравить!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'А мы в двадцать минут впишемся?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Нет, прости Cемен, не могу. После отпуска пообедаем')])");
        sleep(10);
        $this->run_event('ET12.4',"xpath=(//*[contains(text(),'Добрый день, Валерий Семенович!')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В этот раз точно лучше, чем в прошлый. Я ведь того аналитика уволил.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович, мы все ваши замечания учли. Очень старались. В этот раз таких проблем возникнуть не должно.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Это наши корпоративные цвета.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Мы вместе с сотрудниками. Они готовили – я проверял.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Правильно я понял, в презентации оставляем все как есть, добавляем фото, печатаем в цвете? Количество и дату уточнить у секретаря! Всего доброго! ')])");
        sleep(5);

        $this->run_event('E13',"xpath=(//*[contains(text(),'Марина, могу я чем-то помочь?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'А что именно должно поменяться и почему?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же именно привело тебя к такому решению?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я уважаю твое решение, но мне важно знать, что именно на него повлияло.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так причина все-таки во мне, в компании или типе задач?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'А ты можешь рассказать подробнее, какие задачи для тебя интересны? Что вызывает желание работать?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Что же ты раньше об этом не говорила? Довела себя до такого состояния. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давай так договоримся. Ты дашь мне второй шанс. Мне, и компании, и рынку. Мы с тобой вместе завтра проведем анализ всех задач отдела и выберем для тебя самые интересные.')])");
        sleep(5);

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "32");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);

        $this->run_event('E15',"xpath=(//*[contains(text(),'Раиса Романовна, прошу прощения, но я планирую завтрашний день, который у меня последний перед отпуском. Можем мы поговорить об этом в другой раз?')])",'click');
        sleep(5);

        $this->run_event('T7.1',"xpath=(//*[contains(text(),'Я по поводу задания от логистов по выгрузке данных. Трудякин просил сегодня! Ты его сделал?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'В таких случаях надо сразу же спрашивать у меня или у заказчика. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Не ответил? А ты ему звонил?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо,  сейчас поговорю с ним и уточню задание! Но впредь учти – детализация задачи – часть твоей работы! ')])");

        $this->run_event('T7.2',"xpath=(//*[contains(text(),'Егор, ты хотел сегодня получить выгрузку данных! Она тебе все еще нужна?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Но что именно ты ждешь? Я об этом ничего не знаю, исполнитель – Трутнев – тоже. Он тебе два дня назад письмо отправил с уточнениями, ответа не получил!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Именно об этом я и говорю. Трутнев два дня назад написал тебе письмо с уточнениями, но ты не ответил. ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Когда тебе нужны данные?')])");
        sleep(5);

        $this->run_event('T7.5',"xpath=(//*[contains(text(),'Егор, ты посмотрел файл по твоему заданию? Трутнев тебе отправил.')])",'click');
        sleep(5);

        $this->run_event('RS8',"xpath=(//*[contains(text(),'Добрый день, а вы по какому вопросу?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Вероятно, вы плохо себе структуру представляете. Вам нужен директор по продажам.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так в чем же проблема? ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Понимаю. Давайте на цифрах посмотрим, серьезно ли расходится бюджет и ваши представления! ')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я попробую. Поговорю с вашим руководителем. Может быть удастся ее убедить. Спасибо вам за информацию! ')])");
        sleep(5);

        $this->run_event('RS8.1',"xpath=(//*[contains(text(),'Добрый день! Федоров. У меня есть к вам важный вопрос по теме бюджета. Давайте встретимся завтра. Минут на тридцать.')])",'click');
        sleep(5);

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        sleep(30);
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['personal14'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['personal14'],"100");
    }
}
