<?php

class m130423_140359_user_id_key extends CDbMigration
{
	public function safeUp()
	{

        $connection=$this->dbConnection;

        $connection->createCommand("DELETE del.* FROM simulations AS del LEFT JOIN user AS u ON del.user_id = u.id WHERE u.id is null")->execute();
        $this->alterColumn('simulations', 'user_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey('fk_simulations_user_id', 'simulations', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');

        $connection->createCommand("DELETE del.* FROM user_role AS del LEFT JOIN user AS u ON del.user_id = u.id WHERE u.id is null")->execute();
        $this->alterColumn('user_role', 'user_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey('fk_user_role_user_id', 'user_role', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');

        $connection->createCommand("DELETE del.* FROM profile AS del LEFT JOIN user AS u ON del.user_id = u.id WHERE u.id is null")->execute();
        $this->alterColumn('profile', 'user_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey('fk_profile_user_id', 'profile', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');

        $connection->createCommand("DELETE del.* FROM profile_comment AS del LEFT JOIN user AS u ON del.user_id = u.id WHERE u.id is null")->execute();
        $this->alterColumn('profile_comment', 'user_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey('fk_profile_comment_user_id', 'profile_comment', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');

        $connection->createCommand("DELETE del.* FROM profile_comment AS del LEFT JOIN profile AS u ON del.profile_id = u.id WHERE u.id is null")->execute();
        $this->alterColumn('profile_comment', 'profile_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey('fk_profile_comment_profile_id', 'profile_comment', 'profile_id', 'profile', 'id', 'CASCADE', 'CASCADE');

        $connection->createCommand("DELETE del.* FROM privacysetting AS del LEFT JOIN user AS u ON del.user_id = u.id WHERE u.id is null")->execute();
        $this->alterColumn('privacysetting', 'user_id', 'int(10) unsigned NOT NULL');
        $this->addForeignKey('fk_privacysetting_user_id', 'privacysetting', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');

	}

	public function safeDown()
	{
        $this->dropForeignKey('fk_simulations_user_id', 'simulations');
        $this->dropForeignKey('fk_user_role_user_id', 'user_role');
        $this->dropForeignKey('fk_profile_user_id', 'profile');
        $this->dropForeignKey('fk_profile_comment_user_id', 'profile_comment');
        $this->dropForeignKey('fk_profile_comment_profile_id', 'profile_comment');
        $this->dropForeignKey('fk_privacysetting_user_id', 'privacysetting');
	}
}