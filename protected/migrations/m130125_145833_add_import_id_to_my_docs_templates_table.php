<?php

class m130125_145833_add_import_id_to_my_docs_templates_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('my_documents_template', 'import_id', 'VARCHAR(14) DEFAULT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');
	}

	public function down()
	{
		$this->dropColumn('my_documents_template', 'import_id');
	}
}