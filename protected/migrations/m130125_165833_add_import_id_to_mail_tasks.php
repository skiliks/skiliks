<?php

class m130125_165833_add_import_id_to_mail_tasks extends CDbMigration
{
	public function up()
	{
        $this->addColumn('mail_tasks', 'import_id', 'VARCHAR(14) DEFAULT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');
        
	}

	public function down()
	{
		$this->dropColumn('mail_tasks', 'import_id');
	}
}
