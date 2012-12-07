<?php

class m121207_163310_mail_box_update extends CDbMigration
{
	public function up()
	{
            $this->addColumn('mail_box', 'message_id', 'INT(11) DEFAULT NULL');
	}

	public function down()
	{
            $this->dropColumn('mail_box', 'message_id');
	}

}