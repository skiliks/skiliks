<?php

/**
 *
 * @author slavka
 */
class m121217_214152_remove_excel_models extends CDbMigration
{
	public function up()
	{
        $this->dropTable('excel_worksheet_template_cells');
        $this->dropTable('excel_worksheet_cells');
        $this->dropTable('excel_clipboard');
        $this->dropTable('excel_worksheet');
        $this->dropTable('excel_worksheet_template');
        
    }

	public function down()
	{
        
	}
}

