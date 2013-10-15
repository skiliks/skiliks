<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для проверки регистрации корпоративного профиля с и без прохождения симуляции, проверкой ошибок ввода на формах,
 * активации с разными типами email (для SK3054 и SK3055)
 */
class Register_Corporate_Test extends SeleniumTestHelper
{

    public function test_Register_Corporate_SK3054()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('http://test.skiliks.com/ru');

        //это линк регистрации в центре на главной
        $this->optimal_click("link=Получить 10 симуляций бесплатно");

        $this->waitForVisible("//div[@class='testtime']");
        $this->assertText("//div[@class='testtime']", '15 Минут');
        //это кнопка "Начать"
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Введите email','Введите пароль','Подтвердите пароль'));

        $this->type('id=YumProfile_email','empty');
        $this->type('id=YumUser_password','123123');
        $this->type('id=YumUser_password_again','1231231');
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Email введён неверно','Пароли не совпадают'));

        //это лого в футере
        $this->clickAndWait("link=Skiliks");
        $this->clickAndWait("css=footer > a.bigbtnsubmt.freeacess > cufon.cufon.cufon-canvas > canvas");

        $this->type('id=YumProfile_email','empty');
        $this->type('id=YumUser_password','1');
        $this->type('id=YumUser_password_again','1');
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Email введён неверно','Введите не менее 6 символов'));

        //это лого в хедере
        $this->clickAndWait("css=img[alt='Skiliks']");
        $this->clickAndWait("//ul[@id='yw0']/li[4]/a/cufon/canvas");
        $this->clickAndWait("//div[@id='top']/div[2]/div/div/div/div[2]/div[4]/a/cufon[2]/canvas");

        $this->type('id=YumProfile_email','asd@skilikscom');
        $this->type('id=YumUser_password_again','123123');
        $this->clickAndWait("name=yt0");
        $this->assertTrue($this->isTextPresent('Email введён неверно', 'Введите пароль'));

        //генерируем каждый раз новый персональный e-mail для пользователя
        $new_email = "gty1991_1+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500)+(string)rand(1,200);
        $new_email .= "@mail.ru";

        $this->type('id=YumProfile_email',$new_email);
        $this->type('id=YumUser_password','123123');
        $this->type('id=YumUser_password_again','123123');
        $this->optimal_click('id=YumUser_agree_with_terms');
        $this->clickAndWait("name=yt0");

        $this->assertTrue($this->isTextPresent('Активация'));
        sleep(3);
        $this->open(TestUserHelper::getActivationUrl($new_email));

        sleep(5);
        $this->assertTrue($this->isTextPresent('активирован'));
        //$this->optimal_click("xpath=//*[@id='registration_check']");
        $this->optimal_click("xpath=//*[@id='registration_switch']");

        sleep(5);
        $this->waitForVisible("xpath=(//*[contains(text(),'Зарегистрируйтесь, ')])");
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_firstname",'test-name');
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_lastname",'test-surname');

        //генерируем каждый раз новый корпоративный e-mail для пользователя
        $korp_email = "gty1991+";
        $korp_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $korp_email .= "@skiliks.com";

        $this->type('css=#user-account-corporate-form > div.row > div.field > #UserAccountCorporate_corporate_email', $korp_email);
        $this->optimal_click("css=#user-account-corporate-form > div:nth-child(5) > div:nth-child(1) > input:nth-child(1)");

        $this->open(TestUserHelper::getCorporateActivationUrl($korp_email));
        sleep(5);

        $this->assertTrue($this->isVisible("xpath=(//*[contains(text(),'Рабочий')])"));
    }



    public function test_Register_Corporate_SK3055()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('http://test.skiliks.com/ru');

        //это линк регистрации в центре на главной
        $this->optimal_click("link=Получить 10 симуляций бесплатно");

        $this->waitForVisible("//div[@class='testtime']");
        $this->assertText("//div[@class='testtime']", '15 Минут');

        //генерируем каждый раз новый корпоративный e-mail для пользователя
        $new_email = "gty1991+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $new_email .= "@skiliks.com";

        $this->type('id=YumProfile_email',$new_email);
        $this->type('id=YumUser_password','123123');
        $this->type('id=YumUser_password_again','123123');
        $this->optimal_click('id=YumUser_agree_with_terms');
        $this->clickAndWait("name=yt0");

        $this->assertTrue($this->isTextPresent('Активация'));
        sleep(3);
        $this->open(TestUserHelper::getActivationUrl($new_email));

        sleep(5);
        $this->assertTrue($this->isTextPresent('активирован'));
        //нажимаем Начать
        $this->optimal_click("xpath=//*[@id='registration_check']");
        $this->optimal_click("xpath=//*[@id='registration_switch']");

        $this->optimal_click("css=.bigbtnsubmt.start-lite-simulation-now");

        // ожидаем появления иконки телефона
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible(Yii::app()->params['test_mappings']['icons']['phone'])) break;
            } catch (Exception $e) {}
            sleep(1);
        }
        //нажимаем кнопку паузы
        $this->optimal_click("css=.pause");
        //нажимаем завершить демо-симуляцию
        $this->optimal_click("xpath=//*[@id='messageSystemMessageDiv']/div/table/tbody/tr/td[2]/div");
        //ожидаем появления страницы выбора типа аккаунта
        $this->optimal_click("xpath=//*[@id='messageSystemMessageDiv']/div/table/tbody/tr/td/div");

        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=(//*[contains(text(),'Зарегистрируйтесь, ')])")) break;
            } catch (Exception $e) {}
            sleep(1);
        }
        //регистрируем корпоративный профиль
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_firstname",'test-name');
        $this->type("css=#user-account-corporate-form > div.row > div.field > #YumProfile_lastname",'test-surname');
        $this->optimal_click("css=#user-account-corporate-form > div:nth-child(5) > div:nth-child(1) > input:nth-child(1)");
        //ожидаем появления попапа с оценкой
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=//body/div[1]/div[1]/section/aside/div[1]/div[1]/form/div[4]/input")) break;
            } catch (Exception $e) {}
            sleep(1);
        }
        //проверяем, что оценка за демо-симуляцию не посчитана
        $this->waitForVisible("xpath=//div[2]/div[2]/div/div[2]/div[1]/div[2]/div[1]/div/span[2]/span","0");
    }
}