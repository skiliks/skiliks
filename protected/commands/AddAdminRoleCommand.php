<?php

class AddAdminRoleCommand extends CConsoleCommand {

    public function actionIndex($email)
    {
        $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
        if(null === $profile){
            throw new Exception(" User not found ");
        }
        $user = YumUser::model()->findByPk($profile->user_id);
        /* @var $user YumUser */
        if(null === $user){
            throw new Exception(" User not found ");
        }
        $user->is_admin = 1;
        $user->update();
        echo "Done! {$email} is admin.\r\n";
    }

}