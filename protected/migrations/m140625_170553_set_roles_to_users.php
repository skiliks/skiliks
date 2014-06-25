<?php

class m140625_170553_set_roles_to_users extends CDbMigration
{
	public function up()
	{
        $roleSiteUser = YumRole::model()->findByAttributes(['title' => 'Пользователь сайта']);
        $roleAdmin = YumRole::model()->findByAttributes(['title' => 'Админ']);
        $roleSuperAdmin = YumRole::model()->findByAttributes(['title' => 'СуперАдмин']);

        $superAdmin = ['tony@skiliks.com', 'slavka@skiliks.com'];
        $admin = ['masha@skiliks.com', 'tatiana@skiliks.com', 'leah.levin@skiliks.com', 'nina@skiliks.com ', 'ivan@skiliks.com'];

        $connection = Yii::app()->db;
        /** @var YumUser $user */
        foreach (YumUser::model()->findAll() as $user) {
            if (in_array($user->profile->email, $superAdmin)) {
                $command = $connection->createCommand(sprintf(
                    ' INSERT INTO `user_role` () VALUE (%s, %s); ',
                    $user->id,
                    $roleSuperAdmin->id
                ));
                $command->execute();
                echo '!';
            } elseif (in_array($user->profile->email, $admin)) {
                $command = $connection->createCommand(sprintf(
                    ' INSERT INTO `user_role` () VALUE (%s, %s); ',
                    $user->id,
                    $roleAdmin->id
                ));
                $command->execute();
                echo '+';
            } else {
                $command = $connection->createCommand(sprintf(
                    ' INSERT INTO `user_role` () VALUE (%s, %s); ',
                    $user->id,
                    $roleSiteUser->id
                ));
                $command->execute();
                echo '.';
            }
        }
	}

	public function down()
	{
        $connection = Yii::app()->db;
        $command = $connection->createCommand(sprintf(
            ' TRUNCATE `user_role`; '
        ));
        $command->execute();
	}
}