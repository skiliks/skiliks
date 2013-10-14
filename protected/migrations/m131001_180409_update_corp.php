<?php

class m131001_180409_update_corp extends CDbMigration
{
	public function up()
	{
        if(Yii::app()->params['runMigrationOn'] === 'production'){
            $users = YumUser::model()->findAll();
        }elseif(Yii::app()->params['runMigrationOn'] === 'live'){
            $users = [];
            $ids = [135,136,127,134,105,126,123,142,144,145,146,143,147,150,148,151];
            foreach($ids as $id){
                $tmp_user = YumUser::model()->findByAttributes(['id'=>$id]);
                if(null !== $tmp_user){
                    $users[] = $tmp_user;
                }
            }

        } else {
            return true;
        }
        $tmp_emails = [];
        /* @var $user YumUser */
        echo 'Users at all: '.count($users);
        foreach($users as $user) {
            MailHelper::sendNoticeEmail($user);
        }
        file_put_contents(__DIR__.'/../runtime/m131001_180409_update_corp.log', json_encode($tmp_emails));
        return true;
	}

	public function down()
	{
		echo "m131001_180409_update_corp migration down.\n";
		return true;
	}

}