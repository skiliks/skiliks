<?php

/**
 * Class TestUserHelper
 * @todo: move to UserService
 */

class TestUserHelper
{
    public static function addUser($account=null)
    {
        if($account === "personal") {
            $email = 'personal_user@skiliks.com';
        }elseif($account === "corporate"){
            $email = 'corporate_user@skiliks.com';
        }else{
            $email = 'test_user@skiliks.com';
        }

        /* @var $YumUser YumUser */
        /* @var $YumProfile YumProfile */
        $YumProfile = YumProfile::model()->findByAttributes(['email'=>$email]);

        if($YumProfile === null) {
            $YumUser = new YumUser('registration');
            $YumProfile = new YumProfile('registration');
        }else{
            $YumUser = YumUser::model()->findByPk($YumProfile->user_id);
            $YumUser->deleteByPk($YumUser->id);
            unset($YumUser);
            unset($YumProfile);
            $YumUser = new YumUser('registration');
            $YumProfile = new YumProfile('registration');
        }
        $YumUser->attributes = ['password'=>'123123', 'password_again'=>'123123', 'is_check'=>'1'];
        $YumProfile->attributes = ['email'=>$email];
        $YumUser->is_check = (int)$YumUser['is_check'];
        $YumUser->setUserNameFromEmail($YumProfile->email);
        $YumProfile->updateFirstNameFromEmail();
        $YumUser->register($YumUser->username, $YumUser->password, $YumProfile);
        $YumUser->activationKey = '1';
        $YumUser->status = 1;
        $YumUser->update();

        if($account === "personal") {

            $accountPersonal = new UserAccountPersonal;
            $accountPersonal->user_id = $YumUser->id;
            $accountPersonal->industry_id = 3;
            $accountPersonal->professional_status_id = 3;
            $accountPersonal->save();

            $YumProfile->firstname = 'Ivan';
            $YumProfile->lastname  = 'Ivanov';
            $YumProfile->timestamp = gmdate("Y-m-d H:i:s");
            $action = YumAction::model()->findByAttributes(['title' => UserService::CAN_START_FULL_SIMULATION]);
            $permission = new YumPermission();
            $permission->principal_id = $YumUser->id;
            $permission->subordinate_id = $YumUser->id;
            $permission->type = 'user';
            $permission->action = $action->id;
            $permission->template = 1;
            $permission->save();

        } elseif ($account === "corporate") {
            $accountCorporate = new UserAccountCorporate;
            $accountCorporate->user_id = $YumUser->id;
            $accountCorporate->is_corporate_email_verified = 1;

            // todo: take care about user timezone
            $accountCorporate->corporate_email_verified_at = date('Y-m-d H:i:s');
            $accountCorporate->generateActivationKey();
            $accountCorporate->save(false);

            // set Lite tariff by default
            $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);

            // update account tariff
            $accountCorporate->setTariff($tariff);
            $accountCorporate->save();
        }
    }
}