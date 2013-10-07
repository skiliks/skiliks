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
        foreach($users as $user) {
            if($user->isCorporate() && $user->account_corporate->corporate_email !== null && $user->profile->email !== $user->account_corporate->corporate_email) {
                $personal_email =  $user->profile->email;
                $tmp_emails[$user->id] = $personal_email;

                $corporate_email =  $user->account_corporate->corporate_email;
                echo($corporate_email.' => '.$personal_email."\r\n");
                $body = "
                    Приветствуем, {$user->profile->firstname}!<br/>
                    <br/>
                    Мы упростили систему регистрации, и теперь с каждым аккаунтом будет связан<br/>
                    только один адрес электронной почты. С вашим корпоративным аккаунтом связано<br/>
                    две почты {$personal_email} и {$corporate_email}. В результате обновления ваш<br/>
                    логин для входа в систему теперь {$corporate_email}. Пароль остался старый.<br/>
                    <br/>
                    Ваш skiliks.com.<br/>
                ";
                $mail = array(
                    'from' => Yum::module('registration')->registrationEmail,
                    'to' => $personal_email,
                    'subject' => 'Обновление регистрации skiliks.com',
                    'body' => $body,
                    'embeddedImages' => [],
                );
                MailHelper::addMailToQueue($mail);
                $user->profile->email = $corporate_email;
                $user->profile->update();
            }
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