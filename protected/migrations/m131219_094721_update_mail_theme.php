<?php

class m131219_094721_update_mail_theme extends CDbMigration
{
	public function up()
	{
        $this->addColumn('outbox_mail_themes', 'wr', "varchar(5) default null comment 'Правильная(R), не правильная(W) и нейральная темы(N)'");
	}

	public function down()
	{
        $this->dropColumn('outbox_mail_themes', 'wr');
	}
}