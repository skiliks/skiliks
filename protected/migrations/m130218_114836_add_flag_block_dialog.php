<?php

class m130218_114836_add_flag_block_dialog extends CDbMigration
{
	public function up()
	{
        $this->createTable('flag_block_dialog', array(
            'flag_code'   => 'VARCHAR(5) NOT NULL',
            'dialog_code' => 'VARCHAR(10) NOT NULL',
            'value'       => 'boolean',
            'import_id'   => 'VARCHAR(14)',
        ));

        $this->addForeignKey(
            'fk_flag_block_dialog_flag_code',
            'flag_block_dialog',
            'flag_code',
            'flag',
            'code',
            'CASCADE',
            'CASCADE'
        );
    }

	public function down()
	{
		$this->dropTable('flag_block_dialog');
	}
}