<?php

class m130508_154136_add_is_was_saved_to_mydocs extends CDbMigration
{
	public function up()
	{
        $this->addColumn('my_documents', 'is_was_saved', 'TINYINT(1) DEFAULT 0');
	}

	public function down()
	{
        $this->dropColumn('my_documents', 'is_was_saved');
	}
}