<?php

class m130116_124329_mail_box_subject_remove extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('mail_box', 'subject');
	}

	public function down()
	{
		$this->addColumn('mail_box', 'subject', 'varchar(255) DEFAULT NULL');
	}

}