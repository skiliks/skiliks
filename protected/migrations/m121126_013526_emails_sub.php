<?php

class m121126_013526_emails_sub extends CDbMigration
{
	public function up()
	{
        $this->createTable('emails_sub', array(
            'id'         => 'pk',
            'email'     => 'VARCHAR(255) NOT NULL'
        ));
        $this->createIndex('email', 'emails_sub', 'email', true);
	}

	public function down()
	{
		$this->dropTable('emails_sub');
	}

}