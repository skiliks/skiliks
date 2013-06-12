<?php

class RemoveAdminRoleCommand extends CConsoleCommand {

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
        $user->is_admin = 0;
        $user->update();
        echo "Done! {$email} is not admin.\r\n";
    }

}