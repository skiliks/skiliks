<?php

class m121217_115709_add_reply_all extends CDbMigration
{
	public function up()
	{
        $this->alterColumn(
            'mail_template',
            'type_of_importance',
            "ENUM('none','2_min','plan','info','first_category', 'reply_all') DEFAULT 'none' COMMENT 'Is it spam, is it impotrant etc. None - not desided by game autor jet.'");
    }

	public function down()
	{
		echo "m121217_115709_add_reply_all does not support migration down.\n";
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