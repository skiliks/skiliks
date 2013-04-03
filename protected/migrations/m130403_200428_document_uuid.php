<?php

class m130403_200428_document_uuid extends CDbMigration
{
	public function up()
	{
        $this->addColumn('my_documents', 'uuid', 'VARCHAR(255) NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('simulations', 'uuid');
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