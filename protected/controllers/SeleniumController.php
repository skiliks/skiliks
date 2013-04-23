<?php

class SeleniumController extends AjaxController {

    public function actionAddUser()
    {
        $test_user = [
            'username' => 'test_user'
        ];

        /**/
        $YumUser = YumUser::model()->findByAttributes(['username'=>$test_user['username']]);

        if($YumUser === null) {
            $YumUser = new YumUser('registration');

        }else{
            $YumProfile = YumUser::model()->findByAttributes(['username'=>$YumUser->id]);
            $YumUser->delete();
            $YumUser = new YumUser();
        }

        $YumUser->username = 'test_user';

        $account = Yii::app()->request->getParam('account', null);

        if($account === "personal") {

        }elseif($account === "corporate"){

        }else{

        }

        $YumUser->save();
    }

}