<?php

class m130228_140026_remove_old_excel_tables extends CDbMigration
{
	public function up()
	{
        $this->dropTable('excel_document');
        $this->dropTable('excel_document_template');
	}

	public function down()
	{
		echo "m130228_140026_remove_old_excel_tables does not support migration down.\n";
	}
}