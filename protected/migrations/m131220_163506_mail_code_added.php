<?php

class m131220_163506_mail_code_added extends CDbMigration
{
	public function up()
	{
        $this->addColumn('outbox_mail_themes', 'mail_code', 'varchar(10) default null');
	}

	public function down()
	{
        $this->dropColumn('outbox_mail_themes', 'mail_code');
	}

}