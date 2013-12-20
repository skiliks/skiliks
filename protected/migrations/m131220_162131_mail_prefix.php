<?php

class m131220_162131_mail_prefix extends CDbMigration
{
	public function up()
	{
        $this->addColumn('outbox_mail_themes', 'mail_prefix', 'varchar(10) default null');
	}

	public function down()
	{
        $this->dropColumn('outbox_mail_themes', 'mail_prefix');
	}
}