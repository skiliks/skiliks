<?php

class m131002_151354_del extends CDbMigration
{
    public function safeUp()
    {
        $users = YumUser::model()->findAll();
        /* @var $user YumUser */
        foreach($users as $user) {
            if($user->isAnonymous() && false === $user->isActive()){
                var_dump($user->id);
                $user->deleteByPk($user->id);
            }
        }
    }

    public function down()
    {
        echo "migration down.\n";
        return true;;
    }


}