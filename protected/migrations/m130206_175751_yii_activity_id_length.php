<?php

class m130206_175751_yii_activity_id_length extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('activity', 'id', 'VARCHAR(60) CHARACTER SET utf8 NOT NULL');
        $this->alterColumn('activity_action', 'activity_id', 'VARCHAR(60) CHARACTER SET utf8 NOT NULL');
        $this->addForeignKey('activity_action_action_id', 'activity_action', 'activity_id', 'activity', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		echo "m130206_175751_yii_activity_id_length does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}