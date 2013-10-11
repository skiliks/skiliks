<?php

/**
 * Class TestUserHelper for selenium tests
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
            unset($YumProfile);
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
        $YumUser->attributes = ['password'=>'123123', 'password_again'=>'123123'];
        $YumProfile->attributes = ['email'=>$email];
        $YumUser->setUserNameFromEmail(strtolower($YumProfile->email));
        $YumUser->agree_with_terms = YumUser::AGREEMENT_MADE;
        $YumProfile->updateFirstNameFromEmail();
        $YumUser->register($YumUser->username, $YumUser->password, $YumProfile);
        $YumUser->activationKey = '1';
        $YumUser->status = 1;
        if(false === $YumUser->save(false)){
            throw new Exception(" Fail ");
        }
        //$YumProfile->user_id = $YumUser->id;
        $YumProfile->firstname = 'Ivan';
        $YumProfile->lastname  = 'Ivanov';
        $YumProfile->timestamp = time();
        if(false === $YumProfile->save(false)){
            throw new Exception(" Fail ");
        }

        if($account === "personal") {

            $accountPersonal = new UserAccountPersonal;
            $accountPersonal->user_id = $YumUser->id;
            $accountPersonal->industry_id = 3;
            $accountPersonal->professional_status_id = 3;
            if(false === $accountPersonal->save(false)){
                throw new Exception(" Fail ");
            }
            $action = YumAction::model()->findByAttributes(['title' => UserService::CAN_START_FULL_SIMULATION]);
            $permission = new YumPermission();
            $permission->principal_id = $YumUser->id;
            $permission->subordinate_id = $YumUser->id;
            $permission->type = 'user';
            $permission->action = $action->id;
            $permission->template = 1;
            if(false === $permission->save(false)){
                throw new Exception(" Fail ");
            }

        } elseif ($account === "corporate") {
            $accountCorporate = new UserAccountCorporate;
            $accountCorporate->user_id = $YumUser->id;

            if(false === $accountCorporate->save(false)){
                throw new Exception(" Fail ");
            }
            // set Lite tariff by default
            $tariff = Tariff::model()->findByAttributes(['slug' => Tariff::SLUG_LITE]);

            // update account tariff
            $accountCorporate->setTariff($tariff, false);

            if(false === $accountCorporate->save(false)){
                throw new Exception(" Fail ");
            }
        }
    }

    public static function getActivationUrl($email) {
        $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
        if(null === $profile){
            throw new Exception(" User not found ");
        }

        $temp = (isset($_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST']:null;
        $_SERVER['HTTP_HOST'] = self::getHost(Yii::app()->params['frontendUrl']);
        /* @var $profile YumProfile */
        $host = $profile->user->getActivationUrl();
        if($temp){
            unset($_SERVER['HTTP_HOST']);
        }else{
            $_SERVER['HTTP_HOST'] = $temp;
        }
        return str_replace('/usr/bin', '', $host); //
    }

    protected static function getHost($url){
        preg_match('@^(?:http://)?([^/]+)@i',
            $url, $matches);
        $host = $matches[1];

        //preg_match('/[^.]+\.[^.]+$/', $host, $matches);
        return $host;
    }
}