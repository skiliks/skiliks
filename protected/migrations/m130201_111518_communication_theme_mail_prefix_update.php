<?php

class m130201_111518_communication_theme_mail_prefix_update extends CDbMigration
{
	public function up()
	{
        $this->update('mail_prefix', ['code' => 'rere'], "code='double_re'");
        $this->update('mail_prefix', ['code' => 'rerere'], "code='triple_re'");
	}

	public function down()
	{
		echo "m130201_111518_communication_theme_mail_prefix_update does not support migration down.\n";
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