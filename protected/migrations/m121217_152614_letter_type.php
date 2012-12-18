<?php

class m121217_152614_letter_type extends CDbMigration
{
	public function up()
	{
            $this->addColumn("mail_box", "letter_type", "varchar(10) NOT NULL AFTER `message_id`");
	}

	public function down()
	{
            $this->dropColumn("mail_box", "letter_type");
	}

}