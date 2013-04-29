<?php

class m130429_104933_feedback extends CDbMigration
{
	public function up()
	{
        $this->createTable('feedback', [
            'id' => 'pk',
            'theme' => 'varchar(200) not null',
            'message' => 'text',
            'email' => 'varchar(100)'
        ]);
	}

	public function down()
	{
		$this->dropTable('feedback');
	}
}