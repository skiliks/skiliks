<?php

class m130925_115101_emails_queue extends CDbMigration
{
	public function up()
	{
        $this->createTable('emails_queue', [
            'id'=>'pk',
            'subject'=>'varchar(200)',
            'sender_email' => 'varchar(200)',
            'recipients'=>'text',
            'copies'=> 'text',
            'body' => 'longblob',
            'attachments' => 'text',
            'created_at' => 'datetime',
            'sended_at' => 'datetime default null',
            'status' => 'varchar(30)',
            'errors' => 'longblob default null'
        ]);
	}

	public function down()
	{
        $this->dropTable('emails_queue');
	}

}