<?php

//пока что быдловерсия теста, который ходит на страницу регистрации по разным маппингам
// и проверяет error msg на странице регистрации
class AddVacancy_and_Feedback_Test extends SeleniumTestHelper
{

    public function test_addVacancy_add_feedback()
    {
        //cоздаем корпоративного пользователя
        TestUserHelper::addUser("corporate");

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');

        //клик на "вход" на главной
        $this->optimal_click("css=a.sign-in-link > cufon.cufon.cufon-canvas > canvas");
        //заполняем инпуты в поп-апе авторизации
        $this->type('id=YumUserLogin_username','corporate_user@skiliks.com');
        $this->type('id=YumUserLogin_password','123123');
        //клик на "вход" в поп-апе авторизации
        $this->optimal_click("name=yt0");


        //Раскоментировать когда Ваня поправит addUser

        //проверяем что у нового корпоративного юзера 10 симуляций после регистрации
        //$this->assertText("css=span.brightblock > cufon.cufon.cufon-canvas > canvas", '10');
        //проверяем что у нового корпоративного Lite тариф после регистрации
        //$this->assertText("//div[@id='simulations-counter-box']/div[2]/a[@class='brightblock']",'Lite');


        //клик по "Обратная связь" на дэшборде корпоративного пользователя
        $this->optimal_click("//div[@id='top']/div/section/aside/div[3]/a/cufon[2]/canvas");
        //заполняем поле почты
        $this->type('id=Feedback_email','asd');
        //клик по Отправить
        $this->optimal_click("name=submit");
        //ловим err msg
        $this->assertTrue($this->isTextPresent('Введите сообщение','Email введён неверно'));
        //меняем тему обращения
        $this->optimal_click("//div[@class='form-input success']//a[@class='sbSelector']");
        $this->optimal_click('link=Личный кабинет');
        //вводим текст обращения
        $this->type('id=Feedback_message','hi');
        $this->type('id=Feedback_email','asd@skiliks.com');
        $this->optimal_click("name=submit");
        //закрываем поп-ап
        $this->optimal_click("css=a.popupclose");

        /*
        sleep(2);
        //Клик на "Мой профиль"
        $this->optimal_click("//a[@href='/profile']");
        sleep(2);
        //Клик на вкладку "Вакансии"
        $this->optimal_click("link=Вакансии");
        sleep(2);
        $this->optimal_click("link=Добавить");//это кнопка "добавить на вкладке вакансии
        sleep(2);
        $this->optimal_click("name=add");//это кнопка "добавить" уже на странице добавление вакании
        //мы кликнули добавить при пустых инпутах. теперь проверяем наличие 4х сообщений о ошибке
        $this->assertTrue($this->isTextPresent('Выберите профессиональную область',
            'Выберите уровень позиции','Выберите специализацию', 'Введите название вакансии'));
        sleep(2);
        //вводим валидные данные и добавляем вакансию
        //$this->optimal_click("//div[@class='form form-vacancy']//div[@class='row shortSelector']//a[@class='sbToggle']");
        $this->optimal_click("//div[@class='form form-vacancy']//div[1]//a[@class='sbToggle']");
        sleep(2);
        $this->optimal_click("link=Автомобильный бизнес");
        sleep(2);
        $this->optimal_click("//div[@class='form form-vacancy']//div[2]//a[@class='sbToggle']");
        sleep(2);
        $this->optimal_click("link=Специалист");
        sleep(2);
        $this->optimal_click("//div[@class='form form-vacancy']//div[3]//a[@class='sbToggle']");
        sleep(2);
        $this->optimal_click("link=Развитие бизнеса");
        sleep(2);
        $this->type('id=Vacancy_label','Шофер-виртуоз');
        sleep(3);
        $this->optimal_click("//div[@class='row buttons']/input[@type='submit']");*/
        $this->stop();
    }
}