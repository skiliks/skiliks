<?php

class m130227_194230_import_phrases extends CDbMigration
{
	public function up()
	{
        $this->createTable('mail_constructor', [
            'code' => 'VARCHAR(11) NOT NULL PRIMARY KEY',
            'import_id' => 'VARCHAR(60) NOT NULL'
        ]);
        $this->alterColumn('mail_phrases', 'code', 'VARCHAR(11)');
        $this->addForeignKey('mail_phrases_constructor', 'mail_phrases', 'code', 'mail_constructor', 'code', 'CASCADE', 'CASCADE');
        $this->addColumn('mail_phrases', 'import_id', 'VARCHAR(60)');
	}

	public function down()
	{
		$this->dropColumn('mail_phrases', 'import_id');
        $this->dropTable('mail_constructor');
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