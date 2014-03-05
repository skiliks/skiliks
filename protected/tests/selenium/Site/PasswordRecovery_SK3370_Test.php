<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы попапа Вход (4й пункт тест-плана по сайту) (для SK3222)
 */
class PasswordRecovery_SK3370_Test extends SeleniumTestHelper
{
    /**
     * test_PasswordRecovery_SK3370() тестирует задачу SKILIKS-3370.
     */
    public function test_PasswordRecovery_SK3370()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->clear_blocked_auth_users();

        $this->open('/ru');
        $email = "selenium.engine@skiliks.com";
        //кликаем на Вход
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->assertTextPresent('Запомнить меня');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery']);
        $this->assertElementPresent(Yii::app()->params['test_mappings']['site']['recovery_email']);

        $this->recoveryAtPopup("","xpath=(//*[contains(text(),'Введите email')])");
        $this->recoveryAtPopup("wrongEmail","xpath=(//*[contains(text(),'Email введён неверно')])");
        $this->recoveryAtPopup("notRegisteredEmail@skiliks.com","xpath=(//*[contains(text(),'Отсутствующий email')])");
        $this->recoveryAtPopup("emailForBaned@skiliks.com","xpath=(//*[contains(text(),'Ваш аккаунт заблокирован')])");
        $this->recoveryAtPopup($email,"xpath=(//*[contains(text(),'На ваш email выслана инструкция по смене пароля.')])");

        //проверить, что письмо есть в очереди событий
        sleep(10);
        if ($this->findRecoveryMailInQueue($email)==true)
        {
            //перереход по ссылке
            $recoveryURL = $this->createRecoveryURL($this->findUserIdByEmail($email));
            $this->open($recoveryURL);
            //изменить пароль на странице восстановления пароля

            $this->optimal_click(Yii::app()->params['test_mappings']['site']['save_new_pass']);
            $this->waitForVisible("xpath=(//*[contains(text(),'Введите пароль')])");
            $this->waitForVisible("xpath=(//*[contains(text(),'Повторите пароль')])");

            $this->saveNewPassword("1","1","xpath=(//*[contains(text(),'Не менее 6 символов')])");
            $this->saveNewPassword("111111","1111111","xpath=(//*[contains(text(),'Пароли не совпадают')])");
            $this->saveNewPassword("skiliks123123","skiliks123123","xpath=(//*[contains(text(),'')])");//"xpath=(//*[contains(text(),'Новый пароль успешно сохранен')])"

            $this->open($recoveryURL);
            $this->waitForVisible("xpath=(//*[contains(text(),'Пароль уже востановлен')])");
        }
        else
            $this->fail("There is no letter for recover password in database");
    }

    private function createRecoveryURL ($id)
    {
        /* @var YumUser $yumUser */
        $yumUser = YumUser::model()->findByPk($id);
        $key = $yumUser->activationKey;
        $email = strtolower($yumUser->profile->email);
        return "/recovery?key=". $key. "&email=". $email;
    }

    private function findUserIdByEmail ($emailU)
    {
        /* @var YumProfile $profile */
        $profile = YumProfile::model()->find('email=:email', array(':email'=>$emailU));
        return $profile->user_id;
    }

    private function findRecoveryMailInQueue ($email)
    {
        /* @var  EmailQueue $letter */
        $criteria=new CDbCriteria;
        $criteria->condition= 'recipients=:recipients';
        $criteria->order= 't.id DESC';
        $criteria->limit= 1;
        $criteria->params= array(':recipients'=>$email);
        try{
            $letter = EmailQueue::model()->find($criteria);
            if (($letter->recipients==$email)&&($letter->subject == "Восстановление пароля для сайта не задан")) // Восстановление пароля для сайта test.skiliks.com
                return true;
            else
                return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function recoveryAtPopup($email,$element)
    {
        $this->type(Yii::app()->params['test_mappings']['site']['recovery_email'], $email);
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['recovery_button']);
        $this->waitForVisible($element);
    }

    public function saveNewPassword($firstEmail, $secondEmail, $element)
    {
        $this->type(Yii::app()->params['test_mappings']['site']['change_pass'], $firstEmail);
        $this->type(Yii::app()->params['test_mappings']['site']['verify_pass'], $secondEmail);
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['save_new_pass']);
        $this->waitForVisible($element);
    }
}