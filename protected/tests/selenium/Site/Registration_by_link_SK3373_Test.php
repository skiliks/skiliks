<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы регистрации персонального аккаунта по ссылке от корпоративного (для SK3373 и SK3364)
 */
class Registration_by_link_SK3373_Test extends SeleniumTestHelper
{
    /**
     * test_Registration_by_link_SK3373() тестирует задачу SKILIKS-3373
     */
    public function test_Registration_by_link_Accept_SK3373()
    {
        // Пока считаем, что позиция уже добавлена пользователем
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->clear_blocked_auth_users();

        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'selenium.engine@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);

        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['username'])=="seleniumEngine");

        $invites="-". $this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit']);

        $this->open('/admin_area/dashboard');
        $this->waitForVisible(Yii::app()->params['test_admin_mappings']['pages_list']['home']);
        $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['current_user_details']);
        $this->waitForVisible(Yii::app()->params['test_admin_mappings']['corporate_info']['change_password']);
        $this->type(Yii::app()->params['test_admin_mappings']['corporate_info']['add_sim_amount_text'],$invites);
        $this->optimal_click(Yii::app()->params['test_admin_mappings']['corporate_info']['add_sim_amount_btn']);
        $this->waitForTextPresent("Количество доступных симуляций для");

        $this->open('/dashboard');
        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);

        //проверить, что 0 симуляций на счету
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='0');

        //добавить нужное кол-во симуляций себе в аккаунт
        $this->open('/invite/add-10');
        $this->waitForTextPresent("Вам добавлено 10 приглашений!");
        $this->open('/dashboard');
        $this->waitForTextPresent("Рабочий кабинет");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='10');

        //негативные кейсы на валидацию ошибок имени, фамилии, почты, позиции

        //все пустые значения
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->assertTextPresent("Введите имя");
        $this->assertTextPresent("Введите фамилию");
        $this->assertTextPresent("Введите email");

        //имя и фамилия введены, почта неправильная
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteName'],"InviteName");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteSurname'],"InviteSurname");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],"email");
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->assertTextPresent("Email введён неверно");

        //почта своя
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],"selenium.engine@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->waitForVisible("xpath=(//*[contains(text(),'Данный пользователь с e-mail: selenium.engine@skiliks.com')])");
        $this->optimal_click("xpath=//div[4]/div[1]/a/span");
        //$this->assertTextPresent("Действие невозможно");

        //почта корпоративная
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],"selenium.assessment@skiliks.com");
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->waitForVisible("xpath=(//*[contains(text(),'Данный пользователь с e-mail: selenium.assessment@skiliks.com')])");
        $this->optimal_click("xpath=//div[4]/div[1]/a/span");

        //выслать уникальному персональному
        $emailForNewInvite = "invite+";
        $emailForNewInvite .= (string)rand(1, 10000)+(string)rand(1,500)+(string)rand(1,200);
        $emailForNewInvite .= "@pers";
        $emailForNewInvite .= (string)rand(1, 500)+(string)rand(1,50)+(string)rand(1,10);
        $emailForNewInvite .= ".skil.com";
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteName'],"InviteName");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteSurname'],"InviteSurname");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],$emailForNewInvite);
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->waitForVisible(Yii::app()->params['test_mappings']['popup_send_invite']['fullName']);
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['popup_send_invite']['fullName'])=="InviteName InviteSurname");
        $this->optimal_click(Yii::app()->params['test_mappings']['popup_send_invite']['send']);
        sleep(3);

        //выслать персональному, которому было отправлено приглашение
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteName'],"InviteName");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteSurname'],"InviteSurname");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],$emailForNewInvite);
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->assertTextPresent("Приглашение уже отправлено");

        //переход по ссылке от работодателя
        $this->open($this->getInviteLink($emailForNewInvite));
        $this->waitForTextPresent("Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию");

        //проверка на валидацию ошибок при регистрации по ссылке
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTextPresent("Введите пароль");
        $this->assertTextPresent("Подтвердите пароль");
        $this->assertTextPresent("Вы должны согласиться с условиями");
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['register_by_link']['invite_name'])=="InviteName");
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['register_by_link']['invite_surname'])=="InviteSurname");

        //очистить поля имя, фамилия
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_name'],"");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_surname'],"");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTextPresent("Введите имя");
        $this->assertTextPresent("Введите фамилию");

        //новое имя и фамилия, выбрать статус, ввести короткие пароли, открыть лиц. соглашение, закрыть, нажать на регистрацию
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_name'],"NewInviteName");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['invite_surname'],"NewInviteSurname");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password'],"123");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password_again'],"123");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['link_terms']);
        $this->waitForVisible("xpath=(//*[contains(text(),'Тестирование навыков менеджера')])");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['close_terms_popup']);
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['checkbox_terms']);
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTextPresent("Не менее 6 символов");

        //пароли, которые не совпадают
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password'],"123123");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password_again'],"1231234");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTextPresent("Пароли не совпадают");

        //правильные пароли, но слишком простые
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password'],"123123");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password_again'],"123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        sleep(3);
        $this->assertTextPresent("Пароль слишком простой");

        //правильные пароли
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password'],"skiliks123123");
        $this->type(Yii::app()->params['test_mappings']['register_by_link']['password_again'],"skiliks123123");
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['register_button']);
        $this->waitForTextPresent("Личный кабинет");

        //проверка, что у персонального пользователя имя и фамилия те, что были введены приглашенным, а не пригласившим
        $this->waitForVisible(Yii::app()->params['test_mappings']['personal']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['personal']['username'])=="NewInviteName");

        //проверка, что в таблице есть приглашение
        $this->waitForVisible(Yii::app()->params['test_mappings']['personal']['accept_invite']);
    }

    public function test_Registration_by_link_Decline_SK3373()
    {
        // Пока считаем, что позиция уже добавлена пользователем
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->clear_blocked_auth_users();

        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'selenium.engine@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);

        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['username'])=="seleniumEngine");

        $invites="-". $this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit']);

        $this->open('/admin_area/dashboard');
        $this->waitForVisible(Yii::app()->params['test_admin_mappings']['pages_list']['home']);
        $this->optimal_click(Yii::app()->params['test_admin_mappings']['home_page']['current_user_details']);
        $this->waitForVisible(Yii::app()->params['test_admin_mappings']['corporate_info']['change_password']);
        $this->type(Yii::app()->params['test_admin_mappings']['corporate_info']['add_sim_amount'],$invites);
        $this->optimal_click(Yii::app()->params['test_admin_mappings']['corporate_info']['add_sim_amount_btn']);
        $this->waitForTextPresent("Количество доступных симуляций для");

        $this->open('/dashboard');
        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);

        //проверить, что 0 симуляций на счету
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='0');

        //добавить нужное кол-во симуляций себе в аккаунт
        $this->open('/invite/add-10');
        $this->waitForTextPresent("Вам добавлено 10 приглашений!");
        $this->open('/dashboard');
        $this->waitForTextPresent("Рабочий кабинет");
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['invites_limit'])=='10');

        //негативные кейсы на валидацию ошибок имени, фамилии, почты, позиции

        //имя и фамилия введены, почта неправильная
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteName'],"InviteName");
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteSurname'],"InviteSurname");
        $emailForNewInvite = "invite+";
        $emailForNewInvite .= (string)rand(1, 10000)+(string)rand(1,500)+(string)rand(1,200);
        $emailForNewInvite .= "@pers";
        $emailForNewInvite .= (string)rand(1, 500)+(string)rand(1,50)+(string)rand(1,10);
        $emailForNewInvite .= ".skil.com";
        $this->type(Yii::app()->params['test_mappings']['corporate']['inviteEmail'],$emailForNewInvite);
        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['sendInvite']);
        sleep(3);
        $this->waitForVisible(Yii::app()->params['test_mappings']['popup_send_invite']['fullName']);
        $this->assertTrue($this->getValue(Yii::app()->params['test_mappings']['popup_send_invite']['fullName'])=="InviteName InviteSurname");
        $this->optimal_click(Yii::app()->params['test_mappings']['popup_send_invite']['send']);
        sleep(3);

        //переход по ссылке от работодателя
        $this->open($this->getInviteLink($emailForNewInvite));
        $this->waitForTextPresent("Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию");

        //проверка возможности отклонить приглашение
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['decline_register']);
        $this->waitForTextPresent("Пожалуйста, укажите причину отказа");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['back_to_registration']);
        $this->waitForTextPresent("Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['decline_register']);
        $this->waitForTextPresent("Пожалуйста, укажите причину отказа");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['confirm_decline_invite']);
        $this->waitForTextPresent("Необходимо указать причину отказа");

        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['decline_reason_0']);
        $this->optimal_click(Yii::app()->params['test_mappings']['register_by_link']['confirm_decline_invite']);

        $this->waitForTextPresent("Спасибо за Ваш ответ!");
    }
}